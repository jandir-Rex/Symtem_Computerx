<?php

namespace App\Http\Controllers\Stand1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\CuotaVenta;
use App\Services\FacturacionSunatService;
use App\Services\PdfComprobanteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class VentaController extends Controller
{
    /** ⚠️ PARCHE DE DESARROLLO TEMPORAL ⚠️ */
    private const ID_DE_RESPALDO = 1; 

    /** 🧾 Punto de venta (POS) */
    public function pos()
    {
        return view('stands.stand1.ventas.pos');
    }

    /** 🔍 Buscar producto */
    public function buscar(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') return response()->json([]);

        $productos = Producto::where('activo', true)
            ->where('stock', '>', 0)
            ->where(function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%")
                    ->orWhere('codigo_barras', 'like', "%{$q}%");
            })
            ->select('id', 'nombre', 'precio_venta', 'codigo_barras', 'stock', 'imagen')
            ->orderBy('nombre')
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'precio_venta' => (float) $p->precio_venta,
                    'codigo_barras' => $p->codigo_barras,
                    'stock' => (int) $p->stock,
                    'imagen' => $p->imagen ? asset('storage/' . $p->imagen) : asset('img/default.png'),
                ];
            });

        return response()->json($productos);
    }

    /** 💾 Registrar venta e iniciar facturación automática */
    public function store(Request $request, FacturacionSunatService $sunatService) 
    {
        // Validación adaptada al frontend
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'nullable|numeric', 
            'tipo_pago' => 'required|string',
            'tipo_venta' => 'required|string|in:contado,credito',
            'tipo_comprobante' => 'nullable|string', 
            'nombre_cliente' => 'required|string|max:255',
            'documento_cliente' => 'nullable|string|max:20',
            'celular_cliente' => $request->tipo_venta === 'credito' ? 'required|string|max:15' : 'nullable|string|max:15',
            'num_cuotas' => 'nullable|integer|min:1',
            'subtotal' => 'nullable|numeric', 
            'igv' => 'nullable|numeric', 
            'total' => 'nullable|numeric', 
        ]);

        $userID = auth()->check() ? auth()->id() : self::ID_DE_RESPALDO;

        DB::beginTransaction();

        try {
            
            // Gestión de Cliente
            $documento = trim($request->documento_cliente ?? '');

            if (!empty($documento)) {
                $cliente = Cliente::firstOrCreate(
                    ['documento' => $documento],
                    [
                        'nombre' => $request->nombre_cliente,
                        'celular' => $request->celular_cliente,
                        'activo' => true
                    ]
                );

                $cliente->update([
                    'nombre' => $request->nombre_cliente,
                    'celular' => $request->celular_cliente,
                ]);
            } else {
                $cliente = Cliente::create([
                    'nombre' => $request->nombre_cliente,
                    'celular' => $request->celular_cliente,
                    'documento' => null,
                    'activo' => true
                ]);
            }

            // Determinar tipo de comprobante (FACTURA si RUC 11 dígitos, BOLETA en otros casos)
            $tipo_comprobante = (strlen($documento) === 11) ? 'FACTURA' : 'BOLETA';

            // ⭐ Calcular totales desde la base de datos (Verificación y Stock)
            $subtotalSinIGV = 0;
            $productosVenta = [];

            foreach ($request->productos as $item) {
                $producto = Producto::find($item['id']);

                if (!$producto) {
                    throw new \Exception("Producto ID {$item['id']} no encontrado");
                }

                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }

                // El precio_venta del Producto es el valor unitario SIN IGV
                $subtotalSinIGV += (float)$producto->precio_venta * (int)$item['cantidad'];
                $productosVenta[] = [
                    'producto' => $producto,
                    'cantidad' => (int)$item['cantidad'],
                ];
            }

            // Calcular IGV y total
            $igv = round($subtotalSinIGV * 0.18, 2);
            $totalConIGV = round($subtotalSinIGV + $igv, 2);

            $pagado = $request->tipo_venta === 'contado';
            $fecha_pago = $pagado ? Carbon::now() : null;

            // ✅ Crear la venta (PENDIENTE de SUNAT)
            $venta = Venta::create([
                'cliente_id' => $cliente->id,
                'user_id' => $userID, 
                'stand_id' => 1,
                'tipo_comprobante' => $tipo_comprobante, 
                'numero_comprobante' => null,
                'tipo_pago' => $request->tipo_venta,
                'metodo_pago' => $request->tipo_pago,
                'pagado' => $pagado,
                'fecha_pago' => $fecha_pago,
                'subtotal' => $subtotalSinIGV, // Base Imponible SIN IGV
                'igv' => $igv, 
                'total' => $totalConIGV, // Total CON IGV
                'observaciones' => $request->observaciones ?? '',
                'celular_cliente' => $request->celular_cliente,
                // Inicializar estados de SUNAT
                'estado_sunat' => 'PENDIENTE_ENVIO', 
                'mensaje_sunat' => 'Venta registrada, iniciando proceso de facturación...',
            ]);

            // ✅ Insertar detalles de venta y actualizar stock
            foreach ($productosVenta as $item) {
                $producto = $item['producto'];
                $cantidad = $item['cantidad'];
                
                $producto->decrement('stock', $cantidad);

                DB::table('detalle_ventas')->insert([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => round($producto->precio_venta * 1.18, 2), // Precio con IGV
                    'subtotal' => round($producto->precio_venta * $cantidad * 1.18, 2), // Total con IGV
                    
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // ✅ Crear cuotas si es venta a crédito
            if ($request->tipo_venta === 'credito' && $request->num_cuotas > 0) {
                $numCuotas = (int)$request->num_cuotas;
                $montoCuota = round($totalConIGV / $numCuotas, 2);

                for ($i = 1; $i <= $numCuotas; $i++) {
                    CuotaVenta::create([
                        'venta_id' => $venta->id,
                        'numero_cuota' => $i,
                        'monto' => $montoCuota,
                        'fecha_vencimiento' => Carbon::now()->addMonths($i),
                        'pagada' => false,
                    ]);
                }
            }
            
            // ⭐⭐⭐ PASO CRÍTICO: LLAMAR AL SERVICIO DE SUNAT ⭐⭐⭐
            if (in_array($venta->tipo_comprobante, ['FACTURA', 'BOLETA'])) {
                $resultadoFacturacion = $sunatService->generarComprobante($venta);
                
                // Si la facturación falla (por rechazo o error del sistema), hacemos rollback
                if (!$resultadoFacturacion['success']) {
                    DB::rollBack();
                    // Log del error para debugging
                    Log::error('Fallo de SUNAT al crear venta: ' . $resultadoFacturacion['error'], [
                        'venta_id' => $venta->id,
                        'codigo_error' => $resultadoFacturacion['codigo'] ?? null
                    ]);
                    // Lanzar excepción para el frontend
                    throw new \Exception("Venta NO Registrada. Error al emitir comprobante: " . $resultadoFacturacion['error']);
                }
            }

            // Si todo ha ido bien (Venta guardada + SUNAT ACEPTADO/RECHAZADO), hacemos commit
            DB::commit();
            
            // Refrescar la venta para obtener los últimos datos de SUNAT
            $venta->refresh(); 
            $mensaje = 'Venta registrada. Comprobante ' . $venta->numero_comprobante . ' ' . $venta->estado_sunat . ' por SUNAT.';

            return response()->json([
                'success' => true, 
                'venta_id' => $venta->id,
                'message' => $mensaje,
                'estado_sunat' => $venta->estado_sunat,
                'numero_comprobante' => $venta->numero_comprobante
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            
            // Logear cualquier error inesperado
            Log::error('Error FATAL al registrar venta (ROLLBACK): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /** ❌ Eliminar venta */
    public function destroy($id)
    {
        $venta = Venta::findOrFail($id);
        $venta->delete();

        return redirect()
            ->route('stands.stand1.ventas.index')
            ->with('success', 'Venta eliminada correctamente.');
    }

    /** 📋 Listado con filtros */
    public function index(Request $request)
    {
        $ventas = Venta::with('cliente')
            ->where('stand_id', 1)
            ->when($request->tipo_pago, fn($q) =>
                $q->where('tipo_pago', $request->tipo_pago)
            )
            ->when($request->estado_credito, function ($q) use ($request) {
                if ($request->estado_credito === 'pagado') {
                    $q->where('tipo_pago', 'credito')
                      ->whereDoesntHave('cuotas', fn($c) => $c->where('pagada', false));
                }

                if ($request->estado_credito === 'pendiente') {
                    $q->where('tipo_pago', 'credito')
                      ->whereHas('cuotas', fn($c) => $c->where('pagada', false));
                }
            })
            ->when(
                $request->filled('fecha_inicio'),
                fn($q) => $q->whereDate('created_at', '>=', $request->fecha_inicio)
            )
            ->when(
                $request->filled('fecha_fin'),
                fn($q) => $q->whereDate('created_at', '<=', $request->fecha_fin)
            )
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = trim($request->buscar);
                $q->whereHas('cliente', fn($c) =>
                    $c->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('documento', 'like', "%{$buscar}%")
                );
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('stands.stand1.ventas.index', compact('ventas'));
    }

    /** 👁️ Mostrar detalle de venta */
    public function show($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'cuotas'])
            ->findOrFail($id);

        return view('stands.stand1.ventas.show', compact('venta'));
    }

    /** 💸 Actualizar pago de una cuota */
    public function pagarCuota(Request $request, $cuota)
    {
        $request->validate([
            'monto_pagado' => 'required|numeric|min:0',
        ]);

        $cuota = CuotaVenta::findOrFail($cuota);

        if ($cuota->pagada) {
            return response()->json(['error' => 'Esta cuota ya fue pagada.'], 422);
        }

        DB::beginTransaction();

        try {
            $montoPagado = (float) $request->monto_pagado;

            if ($montoPagado >= $cuota->monto) {
                $cuota->update([
                    'pagada' => true,
                    'fecha_pago' => now(),
                ]);
            } else {
                $cuota->update([
                    'monto_pagado' => $montoPagado,
                    'pagada' => false,
                ]);
            }

            $venta = $cuota->venta;

            if ($venta->cuotas()->where('pagada', false)->count() === 0) {
                $venta->update([
                    'pagado' => true,
                    'fecha_pago' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado correctamente.',
                'cuota' => $cuota->fresh(),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    // =======================================================================
    // === MÉTODOS: PDF y WHATSAPP ===
    // =======================================================================

    /**
     * 📥 Descarga directa del comprobante PDF.
     */
    public function descargarComprobante($id, PdfComprobanteService $pdfService)
    {
        $venta = Venta::findOrFail($id);

        // Generar o cargar el PDF
        $filePath = $pdfService->generarYGuardar($venta);

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, "Archivo PDF no encontrado.");
        }

        // Descargar el archivo
        return Storage::disk('public')->download($filePath, basename($filePath));
    }

    /**
     * 🔗 Genera el PDF, lo hace accesible y devuelve la URL para WhatsApp.
     */
    public function generarComprobanteYEnviar($id, PdfComprobanteService $pdfService)
    {
        try {
            $venta = Venta::with('cliente')->findOrFail($id);

            // 1. Verificar si la venta tiene número y hash (Aceptada por SUNAT)
            if (empty($venta->numero_comprobante) || empty($venta->hash_sunat)) {
                return response()->json([
                    'success' => false,
                    'error' => 'El comprobante no ha sido emitido o aceptado por SUNAT.'
                ], 422);
            }
            
            // 2. Generar y guardar el PDF
            $filePath = $pdfService->generarYGuardar($venta);
            
            // 3. Crear URL pública
            if (!Storage::disk('public')->exists($filePath)) {
                 throw new \Exception("Error al guardar el PDF en el servidor.");
            }

            $pdfUrl = Storage::disk('public')->url($filePath);
            
            // 4. Devolver respuesta JSON
            return response()->json([
                'success' => true,
                'url' => url($pdfUrl),
                'celular' => $venta->celular_cliente,
                'filename' => basename($filePath),
            ]);

        } catch (\Throwable $e) {
            Log::error('Error al generar PDF para WhatsApp: ' . $e->getMessage(), ['venta_id' => $id]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🆕 DETALLE DE VENTA PARA MODAL (AJAX)
     */
    public function detalleModal($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'cuotas', 'usuario'])
                      ->where('stand_id', 1)
                      ->findOrFail($id);

        return view('stands.stand1.ventas.detalle-modal', compact('venta'));
    }
}