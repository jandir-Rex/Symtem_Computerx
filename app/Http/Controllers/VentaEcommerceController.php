<?php

namespace App\Http\Controllers;

use App\Models\VentaEcommerce;
use App\Models\Producto; // Asegúrate de tener este modelo si restauras stock
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaEcommerceController extends Controller
{
    /**
     * Muestra el listado de ventas (index).
     */
    public function index(Request $request)
    {
        $query = VentaEcommerce::with('cliente')->orderBy('created_at', 'desc');

        // Lógica de Filtrado (la misma que ya revisamos)
        if ($request->filled('buscar')) {
            $searchTerm = $request->input('buscar');
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', "%$searchTerm%")->orWhere('documento', 'like', "%$searchTerm%"))
                ->orWhere(DB::raw("LPAD(id, 6, '0')"), 'like', '%' . str_replace('ECOM-', '', $searchTerm) . '%');
        }
        if ($request->filled('metodo_pago')) $query->where('metodo_pago', $request->input('metodo_pago'));
        if ($request->filled('pagado')) $query->where('pagado', $request->input('pagado'));
        if ($request->filled('fecha_inicio')) $query->whereDate('created_at', '>=', $request->input('fecha_inicio'));
        if ($request->filled('fecha_fin')) $query->whereDate('created_at', '<=', $request->input('fecha_fin'));
        
        $ventas = $query->paginate(20)->withQueryString();

        // Obtener Estadísticas
        $statsQuery = $query->clone();
        $stats = [
            'total_ventas' => $statsQuery->sum('total'),
            'cantidad_ventas' => $statsQuery->count(),
            'pendientes_pago' => $statsQuery->clone()->where('pagado', false)->count(),
            'pagadas' => $statsQuery->clone()->where('pagado', true)->count(),
        ];

        return view('dashboard.ventas-ecommerce.index', compact('ventas', 'stats'));
    }

    /**
     * Muestra el detalle de una venta específica (show).
     */
    public function show(string $id)
    {
        // Nota: Si cambias la ruta a {venta}, Laravel hace la inyección por ti.
        $venta = VentaEcommerce::with('cliente', 'detalles.producto', 'usuario')->findOrFail($id); 
        
        return view('dashboard.ventas-ecommerce.show', compact('venta'));
    }

    /**
     * Acción: Marca una venta como pagada.
     */
    public function marcarPagado(string $id)
    {
        $venta = VentaEcommerce::findOrFail($id);
        
        if ($venta->pagado) {
            return response()->json(['success' => false, 'message' => 'Esta venta ya está marcada como pagada.'], 400);
        }

        $venta->usuario_id = Auth::id(); 
        $venta->pagado = true;
        $venta->save();

        return response()->json(['success' => true, 'message' => 'Venta marcada como pagada con éxito.']);
    }

    /**
     * Acción: Elimina una venta y restaura el stock (destroy).
     */
    public function destroy(string $id)
    {
        $venta = VentaEcommerce::findOrFail($id);
        
        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->increment('stock', $detalle->cantidad);
                }
            }
            $venta->delete();
        });

        return redirect()->route('dashboard.ventas-ecommerce.index')
            ->with('success', 'Venta E-commerce eliminada y stock restaurado.');
    }
    
    /**
     * Acción: Exportar (placeholder).
     */
    public function exportar(Request $request)
    {
        return back()->with('info', 'La funcionalidad de exportar está pendiente de implementación.');
    }
}