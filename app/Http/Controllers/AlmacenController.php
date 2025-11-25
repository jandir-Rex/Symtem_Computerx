<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Imports\ProductosImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;


class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        // Construir query base
        $query = Producto::query();

        // ========================================
        // APLICAR FILTROS USANDO SCOPES DEL MODELO
        // ========================================
        
        // FILTRO: Búsqueda por nombre o código
        if ($request->filled('buscar')) {
            $query->buscar($request->input('buscar'));
        }

        // FILTRO: Categoría
        if ($request->filled('categoria')) {
            $categoria = $request->input('categoria');
            $query->where('categoria', $categoria);
        }

        // FILTRO: Visible en E-commerce
        if ($request->filled('visible_ecommerce')) {
            $visible = $request->input('visible_ecommerce') == '1';
            $query->where('visible_ecommerce', $visible);
        }

        // FILTRO: Estado activo/inactivo
        if ($request->filled('activo')) {
            $activo = $request->input('activo') == '1';
            $query->where('activo', $activo);
        }

        // Obtener productos paginados (mantener filtros en URL)
        $productos = $query->orderBy('nombre')->paginate(15)->withQueryString();

        // ========================================
        // ESTADÍSTICAS
        // ========================================
        $stats = [
            'total_skus' => Producto::count(),
            'total_unidades' => Producto::sum('stock'),
            'alerta_stock_bajo' => Producto::stockBajo()->count()
        ];

        // ========================================
        // PRODUCTOS CRÍTICOS (Stock bajo o sin stock)
        // ========================================
        $productos_criticos = Producto::stockBajo()
            ->orderBy('stock', 'asc')
            ->limit(6)
            ->get();
        
        // Categorías para el select
        $categorias = Producto::getCategorias();

        return view('almacen.index', compact('productos', 'stats', 'productos_criticos', 'categorias'));
    }

    public function create()
    {
        $categorias = Producto::getCategorias();
        return view('almacen.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
            'categoria' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:100',
            'garantia_meses' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'nullable',
            'destacado' => 'nullable',
            'visible_ecommerce' => 'nullable'
        ]);

        // Convertir checkboxes a booleanos
        $validated['activo'] = $request->has('activo') ? true : false;
        $validated['destacado'] = $request->has('destacado') ? true : false;
        $validated['visible_ecommerce'] = $request->has('visible_ecommerce') ? true : false;

        // Calcular precio de venta con IGV (18%)
        $validated['precio_venta'] = round($validated['precio_venta'] * 1.18, 2);

        // Subir imagen si existe
        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $validated['user_id'] = auth()->id() ?? 1;
        Producto::create($validated);

        // 🤖 LIMPIAR CACHÉ DEL CHATBOT
        Cache::forget('chatbot_products_context');

        return redirect()->route('almacen.index')->with('success', '✅ Producto creado correctamente con precio + IGV.');
    }

    public function edit(Producto $producto)
    {
        // Si es petición AJAX, devolver JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);
        }

        // Si no es AJAX, mostrar vista tradicional
        $categorias = Producto::getCategorias();
        return view('almacen.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras,' . $producto->id,
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
            'categoria' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:100',
            'garantia_meses' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'sometimes|boolean',
            'destacado' => 'sometimes|boolean',
            'visible_ecommerce' => 'sometimes|boolean'
        ]);

        // Calcular precio de venta con IGV
        $validated['precio_venta'] = round($validated['precio_venta'] * 1.18, 2);

        // Manejar imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($validated);

        // 🤖 LIMPIAR CACHÉ DEL CHATBOT
        Cache::forget('chatbot_products_context');

        // Si es AJAX, devolver JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Producto actualizado correctamente',
                'producto' => $producto->fresh()
            ]);
        }

        return redirect()->route('almacen.index')->with('success', '✅ Producto actualizado correctamente con precio + IGV.');
    }

    // ========================================
    // TOGGLE E-COMMERCE (AJAX)
    // ========================================
    public function toggleEcommerce(Request $request, Producto $producto)
    {
        try {
            // Obtener el valor del request
            $visible = $request->input('visible_ecommerce');
            
            // Convertir a booleano
            $visible = filter_var($visible, FILTER_VALIDATE_BOOLEAN);

            // Verificar que el producto esté activo si se quiere hacer visible
            if ($visible && !$producto->activo) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ El producto debe estar activo para ser visible en e-commerce'
                ], 400);
            }

            // Verificar que tenga stock si se quiere hacer visible
            if ($visible && $producto->stock <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ El producto debe tener stock disponible para ser visible en e-commerce'
                ], 400);
            }

            // Actualizar visibilidad
            $producto->visible_ecommerce = $visible;
            $saved = $producto->save();

            if (!$saved) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Error al guardar los cambios'
                ], 500);
            }

            // 🤖 LIMPIAR CACHÉ DEL CHATBOT
            Cache::forget('chatbot_products_context');

            return response()->json([
                'success' => true,
                'message' => $visible 
                    ? '✅ Producto ahora visible en e-commerce' 
                    : '✅ Producto oculto del e-commerce',
                'visible_ecommerce' => $producto->visible_ecommerce
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ========================================
    // BUSCAR PRODUCTO POR CÓDIGO (AJAX)
    // ========================================
    public function buscarPorCodigo(Request $request, $codigo)
    {
        $producto = Producto::where('codigo_barras', $codigo)
            ->orWhere('nombre', 'like', "%{$codigo}%")
            ->first();

        if (!$producto) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'producto' => $producto
        ]);
    }

    // ========================================
    // AJUSTAR STOCK (AJAX)
    // ========================================
    public function ajustarStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|exists:productos,id',
            'tipo_ajuste' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $producto = Producto::find($request->producto_id);

        // Ajustar stock según el tipo
        if ($request->tipo_ajuste === 'entrada') {
            $producto->stock += $request->cantidad;
        } else {
            // Verificar que hay stock suficiente
            if ($producto->stock < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente para realizar la salida'
                ], 400);
            }
            $producto->stock -= $request->cantidad;
        }

        $producto->save();

        // 🤖 LIMPIAR CACHÉ DEL CHATBOT
        Cache::forget('chatbot_products_context');

        return response()->json([
            'success' => true,
            'nuevo_stock' => $producto->stock,
            'message' => '✅ Stock ajustado correctamente'
        ]);
    }

    // ========================================
    // IMPORTAR PRODUCTOS DESDE EXCEL
    // ========================================
    public function importar(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('archivo_excel');
            $filePath = $file->getRealPath();

            $import = new ProductosImport();
            $import->import($filePath);

            $importados = $import->getImportados();
            $actualizados = $import->getActualizados();
            $errores = $import->getErrores();

            // 🤖 LIMPIAR CACHÉ DEL CHATBOT
            Cache::forget('chatbot_products_context');

            $mensaje = "✅ Importación completada: {$importados} productos nuevos, {$actualizados} actualizados.";
            
            if (count($errores) > 0) {
                $mensaje .= " ⚠️ " . count($errores) . " filas con errores.";
                return redirect()->route('almacen.index')
                    ->with('warning', $mensaje)
                    ->with('errores_importacion', $errores);
            }

            return redirect()->route('almacen.index')->with('success', $mensaje);
            
        } catch (\Exception $e) {
            return redirect()->route('almacen.index')
                ->with('error', '❌ Error al importar: ' . $e->getMessage());
        }
    }

    // ========================================
    // DESCARGAR PLANTILLA EXCEL
    // ========================================
    public function descargarPlantilla()
    {
        $spreadsheet = new Spreadsheet();
        
        // ===== HOJA 1: INSTRUCCIONES =====
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Instrucciones');
        
        $sheet->setCellValue('A1', '📋 PLANTILLA DE IMPORTACIÓN DE PRODUCTOS');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0d6efd']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        $sheet->setCellValue('A3', '📖 INSTRUCCIONES DE USO:');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        
        $instrucciones = [
            '1. Complete los datos en la pestaña "Datos" siguiendo el formato indicado',
            '2. Los campos marcados con * son OBLIGATORIOS',
            '3. Los precios de venta se calcularán automáticamente con IGV (18%)',
            '4. Use valores 1 (Sí) o 0 (No) para campos booleanos',
            '5. Guarde el archivo y súbalo en el sistema',
            '',
            '⚠️ IMPORTANTE:',
            '    • No modifique los encabezados de las columnas',
            '    • Verifique que los códigos de barras sean únicos',
            '    • Las categorías válidas están en la pestaña "Categorías"'
        ];
        
        $row = 4;
        foreach ($instrucciones as $instruccion) {
            $sheet->setCellValue('A' . $row, $instruccion);
            if (strpos($instruccion, '⚠️') !== false || strpos($instruccion, '•') !== false) {
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
            }
            $row++;
        }
        
        $sheet->setCellValue('A' . ($row + 1), '💬 ¿Necesitas ayuda? Contacta al administrador del sistema');
        $sheet->getStyle('A' . ($row + 1))->getFont()->setItalic(true)->getSize(9);
        
        $sheet->getColumnDimension('A')->setWidth(70);

        // ===== HOJA 2: DATOS =====
        $sheetDatos = $spreadsheet->createSheet();
        $sheetDatos->setTitle('Datos');
        
        $headers = [
            'A1' => ['texto' => 'nombre', 'ancho' => 25],
            'B1' => ['texto' => 'codigo_barras', 'ancho' => 15],
            'C1' => ['texto' => 'precio_compra', 'ancho' => 15],
            'D1' => ['texto' => 'precio_venta', 'ancho' => 15],
            'E1' => ['texto' => 'stock', 'ancho' => 10],
            'F1' => ['texto' => 'stock_minimo', 'ancho' => 12],
            'G1' => ['texto' => 'categoria', 'ancho' => 15],
            'H1' => ['texto' => 'marca', 'ancho' => 15],
            'I1' => ['texto' => 'garantia_meses', 'ancho' => 15],
            'J1' => ['texto' => 'descripcion', 'ancho' => 30],
            'K1' => ['texto' => 'activo', 'ancho' => 8],
            'L1' => ['texto' => 'destacado', 'ancho' => 10],
            'M1' => ['texto' => 'visible_ecommerce', 'ancho' => 15]
        ];
        
        foreach ($headers as $cell => $data) {
            $sheetDatos->setCellValue($cell, $data['texto']);
            $col = substr($cell, 0, 1);
            $sheetDatos->getColumnDimension($col)->setWidth($data['ancho']);
        }
        
        $sheetDatos->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '198754']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
        $sheetDatos->getRowDimension(1)->setRowHeight(25);
        
        // Ejemplos de productos
        $ejemplos = [
            ['Laptop HP Pavilion 15', '7501234567890', '1500.00', '1800.00', '10', '3', 'laptops', 'HP', '12', 'Laptop con procesador Intel Core i5', '1', '1', '1'],
            ['Mouse Logitech G502', '7509876543210', '80.00', '120.00', '50', '10', 'accesorios', 'Logitech', '6', 'Mouse gaming RGB', '1', '0', '1'],
            ['Teclado Mecánico Redragon', '7502345678901', '120.00', '180.00', '25', '5', 'perifericos', 'Redragon', '12', 'Teclado mecánico switches azules', '1', '1', '1']
        ];
        
        $row = 2;
        foreach ($ejemplos as $ejemplo) {
            $col = 'A';
            foreach ($ejemplo as $valor) {
                $sheetDatos->setCellValue($col . $row, $valor);
                $col++;
            }
            $row++;
        }
        
        $sheetDatos->getStyle('A2:M' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8F9FA']
            ]
        ]);

        // ===== HOJA 3: CATEGORÍAS =====
        $sheetCategorias = $spreadsheet->createSheet();
        $sheetCategorias->setTitle('Categorías');
        
        $sheetCategorias->setCellValue('A1', 'CÓDIGO');
        $sheetCategorias->setCellValue('B1', 'NOMBRE CATEGORÍA');
        $sheetCategorias->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ffc107']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $categorias = Producto::getCategorias();
        $row = 2;
        foreach ($categorias as $key => $value) {
            $sheetCategorias->setCellValue('A' . $row, $key);
            $sheetCategorias->setCellValue('B' . $row, $value);
            $row++;
        }
        
        $sheetCategorias->getStyle('A2:B' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);
        
        $sheetCategorias->getColumnDimension('A')->setWidth(20);
        $sheetCategorias->getColumnDimension('B')->setWidth(30);

        // ===== HOJA 4: AYUDA =====
        $sheetAyuda = $spreadsheet->createSheet();
        $sheetAyuda->setTitle('Ayuda');
        
        $sheetAyuda->setCellValue('A1', '❓ GUÍA DE CAMPOS');
        $sheetAyuda->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0dcaf0']],
        ]);
        
        $ayuda = [
            ['Campo', 'Descripción', 'Ejemplo', 'Obligatorio'],
            ['nombre', 'Nombre del producto', 'Laptop HP Pavilion', 'Sí'],
            ['codigo_barras', 'Código de barras único', '7501234567890', 'No'],
            ['precio_compra', 'Precio sin IGV', '1500.00', 'Sí'],
            ['precio_venta', 'Precio sin IGV (se agregará 18%)', '1800.00', 'Sí'],
            ['stock', 'Cantidad disponible', '10', 'Sí'],
            ['stock_minimo', 'Alerta de stock bajo', '3', 'Sí'],
            ['categoria', 'Ver hoja Categorías', 'laptops', 'No'],
            ['marca', 'Marca del producto', 'HP', 'No'],
            ['garantia_meses', 'Meses de garantía', '12', 'No'],
            ['descripcion', 'Descripción detallada', 'Laptop gaming...', 'No'],
            ['activo', '1=Activo, 0=Inactivo', '1', 'No'],
            ['destacado', '1=Destacado, 0=Normal', '0', 'No'],
            ['visible_ecommerce', '1=Visible, 0=Oculto', '1', 'No']
        ];
        
        $row = 3;
        foreach ($ayuda as $fila) {
            $col = 'A';
            foreach ($fila as $valor) {
                $sheetAyuda->setCellValue($col . $row, $valor);
                $col++;
            }
            $row++;
        }
        
        $sheetAyuda->getStyle('A3:D3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9ECEF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        
        $sheetAyuda->getStyle('A4:D' . ($row - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']]]
        ]);
        
        $sheetAyuda->getColumnDimension('A')->setWidth(20);
        $sheetAyuda->getColumnDimension('B')->setWidth(35);
        $sheetAyuda->getColumnDimension('C')->setWidth(25);
        $sheetAyuda->getColumnDimension('D')->setWidth(15);

        // Establecer hoja activa
        $spreadsheet->setActiveSheetIndex(0);
        
        // Generar archivo
        $writer = new Xlsx($spreadsheet);
        $fileName = 'plantilla_productos_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    // ========================================
    // ELIMINAR PRODUCTO
    // ========================================
    public function destroy(Producto $producto)
    {
        try {
            // Verificar si tiene ventas registradas
            if ($producto->detalleVentas()->count() > 0) {
                return redirect()->back()->with('error', '❌ No se puede eliminar el producto porque tiene ventas registradas.');
            }

            // Eliminar imagen si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $nombreProducto = $producto->nombre;
            $producto->delete();

            // 🤖 LIMPIAR CACHÉ DEL CHATBOT
            Cache::forget('chatbot_products_context');

            return redirect()->back()->with('success', "✅ Producto '{$nombreProducto}' eliminado correctamente.");
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '❌ Error al eliminar el producto: ' . $e->getMessage());
        }
    }
}