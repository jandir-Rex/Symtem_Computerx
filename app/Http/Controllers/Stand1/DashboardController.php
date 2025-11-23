<?php

namespace App\Http\Controllers\Stand1;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;

class DashboardController extends Controller



{
    public function index()
    {
        // Aseguramos que el stand activo sea 1
        session(['stand_activo' => 1]);

        // Ventas
        $ventasTotales = Venta::where('stand_id', 1)->sum('total');
        $ventasPendientes = Venta::where('stand_id', 1)->where('pagado', false)->count();
        $ingresosDelDia = Venta::whereDate('created_at', now())->where('stand_id', 1)->sum('total');

        return view('stands.stand1.dashboard', compact(
            'ventasTotales',
            'ventasPendientes',
            'ingresosDelDia'
        ));
    }
}
