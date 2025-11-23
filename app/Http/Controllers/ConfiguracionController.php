<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Configuracion;
use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Cliente;
use Carbon\Carbon;

class ConfiguracionController extends Controller
{
    /**
     * Constructor - Aplicar middleware de autenticación y rol admin
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Mostrar la página principal de configuración
     */
    public function index()
    {
        // Obtener o crear configuración (siempre debe haber un registro con id=1)
        $config = Configuracion::firstOrCreate(
            ['id' => 1],
            [
                'nombre_empresa' => 'VentasSystem',
                'ruc' => '',
                'direccion' => '',
                'telefono' => '',
                'email' => '',
                'igv' => 18,
                'stock_minimo' => 10,
                'dias_credito' => 30,
                'permitir_venta_sin_stock' => false,
                'imprimir_automatico' => true,
                'stand1_nombre' => 'Productos',
                'stand2_nombre' => 'Reparación',
                'stand1_activo' => true,
                'stand2_activo' => true,
            ]
        );
        
        // Obtener estadísticas del sistema
        $totalUsuarios = User::where('activo', true)->count();
        $totalProductos = Producto::count();
        $totalVentas = Venta::count();
        $totalClientes = Cliente::count();
        
        return view('configuracion.index', compact(
            'config',
            'totalUsuarios',
            'totalProductos',
            'totalVentas',
            'totalClientes'
        ));
    }
    
    /**
     * Actualizar información de la empresa
     */
    public function actualizarEmpresa(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:11',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ], [
            'nombre_empresa.required' => 'El nombre de la empresa es obligatorio',
            'ruc.max' => 'El RUC debe tener máximo 11 dígitos',
            'email.email' => 'El formato del email no es válido',
        ]);
        
        $config = Configuracion::firstOrCreate(['id' => 1]);
        $config->update($request->only([
            'nombre_empresa',
            'ruc',
            'direccion',
            'telefono',
            'email'
        ]));
        
        return redirect()->route('configuracion.index')
            ->with('success', '✅ Información de la empresa actualizada correctamente');
    }
    
    /**
     * Actualizar parámetros de ventas
     */
    public function actualizarVentas(Request $request)
    {
        $request->validate([
            'igv' => 'required|numeric|min:0|max:100',
            'stock_minimo' => 'required|integer|min:0',
            'dias_credito' => 'required|integer|min:1',
        ], [
            'igv.required' => 'El IGV es obligatorio',
            'igv.min' => 'El IGV no puede ser negativo',
            'igv.max' => 'El IGV no puede ser mayor a 100%',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo',
            'dias_credito.min' => 'Los días de crédito deben ser al menos 1',
        ]);
        
        $config = Configuracion::firstOrCreate(['id' => 1]);
        $config->update([
            'igv' => $request->igv,
            'stock_minimo' => $request->stock_minimo,
            'dias_credito' => $request->dias_credito,
            'permitir_venta_sin_stock' => $request->has('permitir_venta_sin_stock'),
            'imprimir_automatico' => $request->has('imprimir_automatico'),
        ]);
        
        return redirect()->route('configuracion.index')
            ->with('success', '✅ Configuración de ventas actualizada correctamente');
    }
    
    /**
     * Actualizar configuración de stands
     */
    public function actualizarStands(Request $request)
    {
        $request->validate([
            'stand1_nombre' => 'required|string|max:255',
            'stand2_nombre' => 'required|string|max:255',
        ], [
            'stand1_nombre.required' => 'El nombre del Stand 1 es obligatorio',
            'stand2_nombre.required' => 'El nombre del Stand 2 es obligatorio',
        ]);
        
        $config = Configuracion::firstOrCreate(['id' => 1]);
        $config->update([
            'stand1_nombre' => $request->stand1_nombre,
            'stand2_nombre' => $request->stand2_nombre,
            'stand1_activo' => $request->has('stand1_activo'),
            'stand2_activo' => $request->has('stand2_activo'),
        ]);
        
        return redirect()->route('configuracion.index')
            ->with('success', '✅ Configuración de stands actualizada correctamente');
    }
    
    /**
     * Limpiar caché del sistema
     */
    public function limpiarCache()
    {
        try {
            // Limpiar diferentes tipos de caché
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => '✅ Caché limpiada correctamente. El sistema está optimizado.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Error al limpiar caché: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Crear respaldo de la base de datos
     */
    public function respaldarBD()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            // Crear directorio si no existe
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            // Obtener configuración de base de datos
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            
            // Construir comando mysqldump
            if (empty($password)) {
                $command = sprintf(
                    'mysqldump -u%s -h%s %s > %s',
                    $username,
                    $host,
                    $database,
                    $path
                );
            } else {
                $command = sprintf(
                    'mysqldump -u%s -p%s -h%s %s > %s',
                    $username,
                    $password,
                    $host,
                    $database,
                    $path
                );
            }
            
            // Ejecutar comando
            $output = null;
            $returnVar = null;
            exec($command, $output, $returnVar);
            
            // Verificar si el archivo se creó
            if (file_exists($path) && filesize($path) > 0) {
                // Descargar y eliminar después
                return response()->download($path)->deleteFileAfterSend(true);
            } else {
                // Si falla mysqldump, intentar con exportación manual
                return $this->respaldarBDManual();
            }
            
        } catch (\Exception $e) {
            return redirect()->route('configuracion.index')
                ->with('error', '❌ Error al crear respaldo: ' . $e->getMessage());
        }
    }
    
    /**
     * Respaldo manual de BD (alternativa si mysqldump no está disponible)
     */
    private function respaldarBDManual()
    {
        try {
            $filename = 'backup_manual_' . date('Y-m-d_H-i-s') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            $sql = "-- Respaldo de Base de Datos\n";
            $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
            
            // Obtener todas las tablas
            $tables = DB::select('SHOW TABLES');
            $dbName = config('database.connections.mysql.database');
            
            foreach ($tables as $table) {
                $tableName = $table->{"Tables_in_$dbName"};
                
                // Estructura de la tabla
                $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
                $sql .= "\n\n-- Tabla: $tableName\n";
                $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
                $sql .= $createTable[0]->{"Create Table"} . ";\n\n";
                
                // Datos de la tabla
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "-- Datos de $tableName\n";
                    foreach ($rows as $row) {
                        $values = array_map(function($value) {
                            return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                        }, (array)$row);
                        
                        $sql .= "INSERT INTO `$tableName` VALUES (" . implode(', ', $values) . ");\n";
                    }
                }
            }
            
            file_put_contents($path, $sql);
            
            return response()->download($path)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->route('configuracion.index')
                ->with('error', '❌ Error al crear respaldo manual: ' . $e->getMessage());
        }
    }
    
    /**
     * Exportar datos a Excel
     */
    public function exportarDatos()
    {
        try {
            // Verificar si PhpSpreadsheet está instalado
            if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
                return $this->exportarDatosCSV();
            }
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            // Hoja 1: Ventas
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Ventas');
            
            // Encabezados
            $headers = ['ID', 'Fecha', 'Cliente', 'Total', 'Tipo Pago', 'Estado', 'Stand'];
            $sheet->fromArray($headers, NULL, 'A1');
            
            // Estilo para encabezados
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);
            $sheet->getStyle('A1:G1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle('A1:G1')->getFont()->getColor()->setRGB('FFFFFF');
            
            // Datos
            $ventas = Venta::with('cliente')->get();
            $row = 2;
            foreach ($ventas as $venta) {
                $sheet->setCellValue('A' . $row, $venta->id);
                $sheet->setCellValue('B' . $row, $venta->created_at->format('Y-m-d H:i:s'));
                $sheet->setCellValue('C' . $row, $venta->cliente->nombre ?? 'Público');
                $sheet->setCellValue('D' . $row, $venta->total);
                $sheet->setCellValue('E' . $row, ucfirst($venta->tipo_pago));
                $sheet->setCellValue('F' . $row, $venta->pagado ? 'Pagado' : 'Pendiente');
                $sheet->setCellValue('G' . $row, 'Stand ' . $venta->stand_id);
                $row++;
            }
            
            // Ajustar ancho de columnas
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Hoja 2: Productos
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Productos');
            $headers2 = ['ID', 'Nombre', 'Código', 'Precio', 'Stock', 'Categoría', 'Activo'];
            $sheet2->fromArray($headers2, NULL, 'A1');
            
            $sheet2->getStyle('A1:G1')->getFont()->setBold(true);
            $sheet2->getStyle('A1:G1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('70AD47');
            $sheet2->getStyle('A1:G1')->getFont()->getColor()->setRGB('FFFFFF');
            
            $productos = Producto::with('categoria')->get();
            $row = 2;
            foreach ($productos as $producto) {
                $sheet2->setCellValue('A' . $row, $producto->id);
                $sheet2->setCellValue('B' . $row, $producto->nombre);
                $sheet2->setCellValue('C' . $row, $producto->codigo);
                $sheet2->setCellValue('D' . $row, $producto->precio);
                $sheet2->setCellValue('E' . $row, $producto->stock);
                $sheet2->setCellValue('F' . $row, $producto->categoria->nombre ?? 'Sin categoría');
                $sheet2->setCellValue('G' . $row, $producto->activo ? 'Sí' : 'No');
                $row++;
            }
            
            foreach (range('A', 'G') as $col) {
                $sheet2->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Hoja 3: Clientes
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Clientes');
            $headers3 = ['ID', 'Nombre', 'DNI', 'Teléfono', 'Email', 'Dirección'];
            $sheet3->fromArray($headers3, NULL, 'A1');
            
            $sheet3->getStyle('A1:F1')->getFont()->setBold(true);
            $sheet3->getStyle('A1:F1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFC000');
            $sheet3->getStyle('A1:F1')->getFont()->getColor()->setRGB('000000');
            
            $clientes = Cliente::all();
            $row = 2;
            foreach ($clientes as $cliente) {
                $sheet3->setCellValue('A' . $row, $cliente->id);
                $sheet3->setCellValue('B' . $row, $cliente->nombre);
                $sheet3->setCellValue('C' . $row, $cliente->dni);
                $sheet3->setCellValue('D' . $row, $cliente->telefono);
                $sheet3->setCellValue('E' . $row, $cliente->email);
                $sheet3->setCellValue('F' . $row, $cliente->direccion);
                $row++;
            }
            
            foreach (range('A', 'F') as $col) {
                $sheet3->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Guardar archivo
            $filename = 'exportacion_' . date('Y-m-d_H-i-s') . '.xlsx';
            $path = storage_path('app/exports/' . $filename);
            
            if (!file_exists(storage_path('app/exports'))) {
                mkdir(storage_path('app/exports'), 0755, true);
            }
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($path);
            
            return response()->download($path)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->route('configuracion.index')
                ->with('error', '❌ Error al exportar datos: ' . $e->getMessage());
        }
    }
    
    /**
     * Exportar datos a CSV (alternativa si no hay PhpSpreadsheet)
     */
    private function exportarDatosCSV()
    {
        try {
            $filename = 'exportacion_' . date('Y-m-d_H-i-s') . '.csv';
            $path = storage_path('app/exports/' . $filename);
            
            if (!file_exists(storage_path('app/exports'))) {
                mkdir(storage_path('app/exports'), 0755, true);
            }
            
            $handle = fopen($path, 'w');
            
            // Ventas
            fputcsv($handle, ['=== VENTAS ===']);
            fputcsv($handle, ['ID', 'Fecha', 'Cliente', 'Total', 'Tipo Pago', 'Estado']);
            
            $ventas = Venta::with('cliente')->get();
            foreach ($ventas as $venta) {
                fputcsv($handle, [
                    $venta->id,
                    $venta->created_at->format('Y-m-d H:i:s'),
                    $venta->cliente->nombre ?? 'Público',
                    $venta->total,
                    ucfirst($venta->tipo_pago),
                    $venta->pagado ? 'Pagado' : 'Pendiente'
                ]);
            }
            
            // Separador
            fputcsv($handle, []);
            
            // Productos
            fputcsv($handle, ['=== PRODUCTOS ===']);
            fputcsv($handle, ['ID', 'Nombre', 'Código', 'Precio', 'Stock']);
            
            $productos = Producto::all();
            foreach ($productos as $producto) {
                fputcsv($handle, [
                    $producto->id,
                    $producto->nombre,
                    $producto->codigo,
                    $producto->precio,
                    $producto->stock
                ]);
            }
            
            fclose($handle);
            
            return response()->download($path)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            return redirect()->route('configuracion.index')
                ->with('error', '❌ Error al exportar datos CSV: ' . $e->getMessage());
        }
    }
    
    /**
     * Limpiar datos antiguos del sistema
     */
    public function limpiarDatos()
    {
        try {
            DB::beginTransaction();
            
            $fechaLimite = Carbon::now()->subYears(2);
            
            // Eliminar ventas antiguas (y sus detalles por cascada)
            $ventasEliminadas = Venta::where('created_at', '<', $fechaLimite)
                ->where('pagado', true) // Solo las pagadas
                ->delete();
            
            // Puedes agregar más limpiezas aquí según necesites
            // Por ejemplo: logs antiguos, notificaciones viejas, etc.
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "✅ Se eliminaron {$ventasEliminadas} registros antiguos correctamente"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => '❌ Error al limpiar datos: ' . $e->getMessage()
            ], 500);
        }
    }
}