<?php

namespace App\Http\Controllers\Stand2;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Reparacion;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Aseguramos que el stand activo sea 2
        session(['stand_activo' => 2]);

        // Ventas
        $ventasTotales = Venta::where('stand_id', 2)->sum('total');
        $ventasPendientes = Venta::where('stand_id', 2)->where('pagado', false)->count();
        $ingresosDelDiaVentas = Venta::whereDate('created_at', now())->where('stand_id', 2)->sum('total');
        $ingresosDelDiaReparaciones = Reparacion::whereDate('fecha_entrega_real', now())->where('stand_id', 2)->sum('costo_total');

        $ingresosDelDia = $ingresosDelDiaVentas + $ingresosDelDiaReparaciones;

        // Reparaciones
        $reparacionesActivas = Reparacion::where('stand_id', 2)
            ->whereIn('estado', ['recibido','diagnosticando','en_reparacion','esperando_repuestos'])
            ->count();

        $reparacionesPorEstado = Reparacion::where('stand_id', 2)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total','estado');

        return view('stands.stand2.dashboard', compact(
            'ventasTotales',
            'ventasPendientes',
            'ingresosDelDia',
            'reparacionesActivas',
            'reparacionesPorEstado'
        ));
    }
}
