<?php
// app/Http/Controllers/ClienteController.php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::query();
        
        // Filtro por búsqueda
        if ($request->buscar) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('documento', 'like', '%' . $request->buscar . '%')
                  ->orWhere('telefono', 'like', '%' . $request->buscar . '%')
                  ->orWhere('email', 'like', '%' . $request->buscar . '%');
            });
        }
        
        // Filtro por estado
        if ($request->estado !== null) {
            $query->where('activo', $request->estado);
        }
        
        // Ordenar
        $orden = $request->orden ?? 'nombre';
        $direccion = $request->direccion ?? 'asc';
        $query->orderBy($orden, $direccion);
        
        $clientes = $query->paginate(15);
        
        // Estadísticas
        $stats = [
            'total' => Cliente::count(),
            'activos' => Cliente::where('activo', true)->count(),
            'con_compras' => Cliente::has('ventas')->count(),
            'con_creditos' => Cliente::whereHas('ventas', function($q) {
                $q->where('tipo_pago', 'credito')->where('pagado', false);
            })->count()
        ];
        
        return view('clientes.index', compact('clientes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:20|unique:clientes,documento',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string|max:500'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'documento.unique' => 'Este documento ya está registrado',
            'email.email' => 'El email no es válido',
            'email.unique' => 'Este email ya está registrado'
        ]);
        
        Cliente::create([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'activo' => $request->has('activo')
        ]);
        
        return redirect()->route('clientes.index')
            ->with('success', '✓ Cliente creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        // Cargar relaciones
        $cliente->load(['ventas.detalles', 'reparaciones']);
        
        // Historial de compras
        $historialVentas = $cliente->ventas()
            ->with('detalles.producto')
            ->latest()
            ->take(10)
            ->get();
        
        // Reparaciones
        $historialReparaciones = $cliente->reparaciones()
            ->latest()
            ->take(5)
            ->get();
        
        // Estadísticas del cliente
        $stats = [
            'total_compras' => $cliente->ventas()->count(),
            'monto_total' => $cliente->ventas()->sum('total'),
            'creditos_pendientes' => $cliente->ventas()
                ->where('tipo_pago', 'credito')
                ->where('pagado', false)
                ->count(),
            'monto_creditos' => $cliente->ventas()
                ->where('tipo_pago', 'credito')
                ->where('pagado', false)
                ->sum('total'),
            'ultima_compra' => $cliente->ventas()->latest()->first()?->created_at,
            'reparaciones_total' => $cliente->reparaciones()->count(),
            'reparaciones_pendientes' => $cliente->reparaciones()
                ->whereIn('estado', ['pendiente', 'diagnosticado', 'en_reparacion'])
                ->count()
        ];
        
        return view('clientes.show', compact('cliente', 'historialVentas', 'historialReparaciones', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:20|unique:clientes,documento,' . $cliente->id,
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'direccion' => 'nullable|string|max:500'
        ]);
        
        $cliente->update([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'activo' => $request->has('activo')
        ]);
        
        return redirect()->route('clientes.index')
            ->with('success', '✓ Cliente actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        // Verificar si tiene ventas o reparaciones
        if ($cliente->ventas()->exists() || $cliente->reparaciones()->exists()) {
            return back()->withErrors([
                'error' => 'No se puede eliminar el cliente porque tiene ventas o reparaciones registradas. Puede desactivarlo en su lugar.'
            ]);
        }
        
        $cliente->delete();
        
        return redirect()->route('clientes.index')
            ->with('success', '✓ Cliente eliminado correctamente');
    }
    
    /**
     * Cambiar estado activo/inactivo (AJAX)
     */
    public function toggleEstado(Cliente $cliente)
    {
        $cliente->update(['activo' => !$cliente->activo]);
        
        return response()->json([
            'success' => true,
            'activo' => $cliente->activo,
            'message' => 'Estado actualizado correctamente'
        ]);
    }
    
    /**
     * Buscar cliente por documento o nombre (AJAX)
     */
    public function buscar(Request $request)
    {
        $query = $request->input('query');
        
        $clientes = Cliente::where('activo', true)
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', '%' . $query . '%')
                  ->orWhere('documento', 'like', '%' . $query . '%')
                  ->orWhere('telefono', 'like', '%' . $query . '%');
            })
            ->limit(10)
            ->get(['id', 'nombre', 'documento', 'telefono', 'email']);
        
        return response()->json($clientes);
    }
    
    /**
     * Obtener información de cliente por ID (AJAX)
     */
    public function obtenerInfo(Cliente $cliente)
    {
        return response()->json([
            'success' => true,
            'cliente' => [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'documento' => $cliente->documento,
                'telefono' => $cliente->telefono,
                'email' => $cliente->email,
                'direccion' => $cliente->direccion
            ]
        ]);
    }
    
    /**
     * Historial de compras del cliente (AJAX)
     */
    public function historial(Cliente $cliente)
    {
        $ventas = $cliente->ventas()
            ->with('detalles.producto')
            ->latest()
            ->paginate(10);
        
        return response()->json($ventas);
    }
    
    /**
     * Exportar clientes a CSV
     */
    public function exportar()
    {
        $clientes = Cliente::all();
        
        $filename = 'clientes_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, [
            'ID',
            'Nombre',
            'Documento',
            'Teléfono',
            'Email',
            'Dirección',
            'Estado',
            'Fecha Registro'
        ]);
        
        // Datos
        foreach ($clientes as $cliente) {
            fputcsv($output, [
                $cliente->id,
                $cliente->nombre,
                $cliente->documento,
                $cliente->telefono,
                $cliente->email,
                $cliente->direccion,
                $cliente->activo ? 'Activo' : 'Inactivo',
                $cliente->created_at->format('d/m/Y')
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Reporte de clientes con deuda
     */
    public function reporteDeudores()
    {
        $deudores = Cliente::whereHas('ventas', function($q) {
                $q->where('tipo_pago', 'credito')
                  ->where('pagado', false);
            })
            ->with(['ventas' => function($q) {
                $q->where('tipo_pago', 'credito')
                  ->where('pagado', false);
            }])
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                    'documento' => $cliente->documento,
                    'telefono' => $cliente->telefono,
                    'total_deuda' => $cliente->ventas->sum('total'),
                    'cantidad_creditos' => $cliente->ventas->count()
                ];
            });
        
        return view('clientes.deudores', compact('deudores'));
    }
}