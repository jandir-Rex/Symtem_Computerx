<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\Reparacion;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StandController extends Controller
{
    public function show($standId)
    {
        $user = Auth::user();
        $hoy = Carbon::today();

        // Validar Stand
        if (!in_array($standId, [1, 2])) {
            abort(404, 'Stand no encontrado');
        }

        // === PRODUCTOS DEL STAND ===
        $productos = Producto::where('stand_id', $standId)
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        $totalProductos = $productos->count();
        $stockTotal = $productos->sum('stock');
        $productosBajoStock = $productos->filter(fn($p) => $p->stock <= 3)->count();

        // === VENTAS ===
        $ventasHoy = Venta::where('stand_id', $standId)
            ->whereDate('created_at', $hoy)
            ->count();

        $ingresosHoy = Venta::where('stand_id', $standId)
            ->whereDate('created_at', $hoy)
            ->sum('total');

        // === REPARACIONES ACTIVAS (solo Stand 2) ===
        $reparacionesActivas = 0;
        if ($standId == 2) {
            $reparacionesActivas = Reparacion::where('stand_id', $standId)
                ->where('estado', '!=', 'Entregado')
                ->count();
        }

        // === DATOS PARA LA VISTA ===
        $datos = [
            'productos' => $productos,
            'totalProductos' => $totalProductos,
            'stockTotal' => $stockTotal,
            'productosBajoStock' => $productosBajoStock,
            'ventasHoy' => $ventasHoy,
            'ingresosHoy' => $ingresosHoy,
            'reparacionesActivas' => $reparacionesActivas,
            'standId' => $standId
        ];

        return view("stands.stand{$standId}", $datos);
    }
}
