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
        
        Log::info('🔍 Buscando pedidos para usuario', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);
        
        // 🔥 ESTRATEGIA MÚLTIPLE: Buscar ventas de varias formas
        
        // 1️⃣ Buscar cliente(s) por email (puede haber varios)
        $clientes = Cliente::where('email', $user->email)->get();
        $clienteIds = $clientes->pluck('id')->toArray();
        
        Log::info('👥 Clientes encontrados', [
            'count' => $clientes->count(),
            'cliente_ids' => $clienteIds,
        ]);
        
        // 2️⃣ Buscar ventas por MÚLTIPLES criterios
        $ventas = Venta::query()
            ->where(function($query) use ($clienteIds, $user) {
                // Por cliente_id
                if (!empty($clienteIds)) {
                    $query->whereIn('cliente_id', $clienteIds);
                }
                
                // Por user_id (si el usuario compró estando logueado como admin/vendedor)
                $query->orWhere('user_id', $user->id);
                
                // Por email en metadata (para compras de Stripe/otros)
                $query->orWhereJsonContains('metadata->customer_data->email', $user->email);
            })
            ->whereNull('stand_id') // Solo e-commerce
            ->with(['detalles.producto', 'cliente'])
            ->orderBy('created_at', 'desc')
            ->get(); // Usamos get() para debug, luego paginate()
        
        Log::info('📦 Ventas encontradas (antes de filtros)', [
            'total' => $ventas->count(),
            'ventas_ids' => $ventas->pluck('id')->toArray(),
        ]);
        
        // 3️⃣ FILTRO ADICIONAL: Verificar en metadata si no hay cliente_id
        $ventasFiltradas = $ventas->filter(function($venta) use ($user) {
            // Si tiene cliente_id y coincide, ok
            if ($venta->cliente_id && in_array($venta->cliente_id, Cliente::where('email', $user->email)->pluck('id')->toArray())) {
                return true;
            }
            
            // Si tiene user_id y coincide, ok
            if ($venta->user_id === $user->id) {
                return true;
            }
            
            // Si tiene metadata con el email, ok
            if (!empty($venta->metadata['customer_data']['email']) && 
                $venta->metadata['customer_data']['email'] === $user->email) {
                return true;
            }
            
            return false;
        });
        
        Log::info('✅ Ventas después de filtros', [
            'total_filtradas' => $ventasFiltradas->count(),
            'ventas_ids' => $ventasFiltradas->pluck('id')->toArray(),
        ]);
        
        // 4️⃣ Convertir a colección paginada (simulada)
        $page = request()->get('page', 1);
        $perPage = 15;
        $ventasPaginadas = new \Illuminate\Pagination\LengthAwarePaginator(
            $ventasFiltradas->forPage($page, $perPage),
            $ventasFiltradas->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('orders.index', ['ventas' => $ventasPaginadas]);
    }

    /**
     * Muestra el detalle de un pedido específico
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $venta = Venta::with(['detalles.producto', 'cliente'])
            ->findOrFail($id);

        Log::info('🔍 Verificando acceso a pedido', [
            'venta_id' => $venta->id,
            'user_id' => $user->id,
            'user_email' => $user->email,
            'venta_cliente_id' => $venta->cliente_id,
            'venta_user_id' => $venta->user_id,
            'cliente_email' => $venta->cliente->email ?? null,
            'metadata_email' => $venta->metadata['customer_data']['email'] ?? null,
        ]);

        // ✅ Verificar MÚLTIPLES formas de autorización
        $autorizado = false;
        
        // 1. Por cliente_id + email
        if ($venta->cliente && $venta->cliente->email === $user->email) {
            $autorizado = true;
            Log::info('✅ Autorizado por cliente_id + email');
        }
        
        // 2. Por user_id
        if ($venta->user_id === $user->id) {
            $autorizado = true;
            Log::info('✅ Autorizado por user_id');
        }
        
        // 3. Por metadata (compras de Stripe/otros)
        if (!empty($venta->metadata['customer_data']['email']) && 
            $venta->metadata['customer_data']['email'] === $user->email) {
            $autorizado = true;
            Log::info('✅ Autorizado por metadata email');
        }
        
        if (!$autorizado) {
            Log::warning('❌ Acceso denegado a pedido', [
                'venta_id' => $id,
                'user_id' => $user->id,
            ]);
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
            
            Log::info('📥 Solicitud de descarga de comprobante', [
                'venta_id' => $ventaId,
                'user_id' => $user->id,
                'venta_pagado' => $venta->pagado,
                'venta_numero' => $venta->numero_comprobante,
            ]);
            
            // ✅ Verificar autorización (misma lógica que show())
            $autorizado = false;
            
            if ($venta->cliente && $venta->cliente->email === $user->email) {
                $autorizado = true;
            }
            
            if ($venta->user_id === $user->id) {
                $autorizado = true;
            }
            
            if (!empty($venta->metadata['customer_data']['email']) && 
                $venta->metadata['customer_data']['email'] === $user->email) {
                $autorizado = true;
            }
            
            if (!$autorizado) {
                Log::warning('❌ Descarga no autorizada', ['venta_id' => $ventaId]);
                abort(403, 'No autorizado.');
            }
            
            // Verificar que esté pagado
            if (!$venta->pagado) {
                Log::warning('⚠️ Intento de descarga de pedido no pagado', ['venta_id' => $ventaId]);
                return redirect()->back()->with('error', 'El pedido aún no está pagado.');
            }

            // Generar o recuperar el PDF
            $filePath = $this->pdfService->generarYGuardar($venta);

            // Verificar que existe
            if (!Storage::disk('public')->exists($filePath)) {
                Log::error('❌ PDF no encontrado', [
                    'venta_id' => $ventaId,
                    'file_path' => $filePath,
                    'storage_path' => Storage::disk('public')->path($filePath),
                ]);
                return redirect()->back()->with('error', 'PDF no encontrado');
            }

            Log::info('✅ Descarga de comprobante exitosa', [
                'venta_id' => $ventaId,
                'file_path' => $filePath,
            ]);

            // Descargar el archivo
            $nombreArchivo = basename($filePath);
            return Storage::disk('public')->download($filePath, $nombreArchivo);

        } catch (\Exception $e) {
            Log::error('❌ Error al descargar comprobante', [
                'venta_id' => $ventaId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return redirect()->back()->with('error', 'Error al descargar el comprobante: ' . $e->getMessage());
        }
    }
}