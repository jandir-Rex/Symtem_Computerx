<?php

namespace App\Http\Controllers\Stand1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CuotaVenta;

class CuotaVentaController extends Controller
{
    public function pagarCuota(Request $request, CuotaVenta $cuota)
    {
        $request->validate([
            'monto_pagado' => 'required|numeric|min:0',
        ]);

        if($cuota->pagada){
            return response()->json(['success' => false, 'error' => 'Esta cuota ya fue pagada.']);
        }

        $cuota->monto_pagado = $request->monto_pagado;

        if($cuota->monto_pagado >= $cuota->monto){
            $cuota->pagada = true;
            $cuota->fecha_pago = now();
        }

        $cuota->save();

        return response()->json(['success' => true]);
    }
}
