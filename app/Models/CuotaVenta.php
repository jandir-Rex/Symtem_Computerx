<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuotaVenta extends Model
{
    protected $table = 'cuotas_ventas';

   protected $fillable = [
    'venta_id',
    'numero_cuota',
    'monto',
    'monto_pagado',
    'fecha_vencimiento',
    'pagada',
 ];


    protected $casts = [
        'monto' => 'decimal:2',
        'pagada' => 'boolean',
        'fecha_vencimiento' => 'date',
        'fecha_pago' => 'date',
    ];

    // Relación inversa con Venta
    public function venta()
    {
        return $this->belongsTo(\App\Models\Venta::class, 'venta_id');
    }
}
