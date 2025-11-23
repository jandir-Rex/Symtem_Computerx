<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\PdfComprobanteService;

class OrderController extends Controller
{
    protected $pdfService;

    public function __construct(PdfComprobanteService $pdfService)
    {
        $this->middleware('auth');
        $this->pdfService = $pdfService;
    }

    /**
     * Lista todos los pedidos del usuario autenticado
     */
    public function index()
    {
        $user = Auth::user();
        
        // 🔍 Buscar el cliente por email del usuario autenticado
        $cliente = Cliente::where('email', $user->email)->first();
        
        if (!$cliente) {
            // Si no existe el cliente, mostrar sin pedidos
            $ventas = collect();
            
            Log::info('👤 Usuario sin cliente asociado', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
        } else {
            // ✅ Buscar ventas por cliente_id del e-commerce (stand_id = NULL)
            $ventas = Venta::where('cliente_id', $cliente->id)
                ->whereNull('stand_id') // Solo e-commerce
                ->with(['detalles.producto', 'cliente'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            Log::info('📦 Pedidos encontrados', [
                'user_id' => $user->id,
                'cliente_id' => $cliente->id,
                'total_ventas' => $ventas->total(),
            ]);
        }

        return view('orders.index', compact('ventas'));
    }

    /**
     * Muestra el detalle de un pedido específico
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $venta = Venta::with(['detalles.producto', 'cliente'])
            ->findOrFail($id);

        // ✅ Verificar que el pedido pertenezca al usuario autenticado
        // Comparar por email del cliente
        if ($venta->cliente->email !== $user->email) {
            abort(403, 'No tienes permiso para ver este pedido.');
        }

        return view('orders.show', compact('venta'));
    }

    /**
     * Descargar comprobante PDF
     */
    public function descargarComprobante($ventaId)
    {
        try {
            $user = Auth::user();
            $venta = Venta::with('cliente')->findOrFail($ventaId);
            
            // ✅ Verificar autorización por email del cliente
            if ($venta->cliente->email !== $user->email) {
                abort(403, 'No autorizado.');
            }
            
            // Verificar que esté pagado y tenga comprobante
            if (!$venta->pagado) {
                return redirect()->back()->with('error', 'El pedido aún no está pagado.');
            }

            // Generar o recuperar el PDF
            $filePath = $this->pdfService->generarYGuardar($venta);

            // Verificar que existe
            if (!Storage::disk('public')->exists($filePath)) {
                Log::error('PDF no encontrado', [
                    'venta_id' => $ventaId,
                    'file_path' => $filePath
                ]);
                return redirect()->back()->with('error', 'PDF no encontrado');
            }

            // Descargar el archivo
            $nombreArchivo = basename($filePath);
            return Storage::disk('public')->download($filePath, $nombreArchivo);

        } catch (\Exception $e) {
            Log::error('Error al descargar comprobante', [
                'venta_id' => $ventaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Error al descargar el comprobante.');
        }
    }
}