<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    /** 🧾 Nombre exacto de la tabla */
    protected $table = 'detalle_ventas';

    /** 🧩 Campos permitidos para asignación masiva */
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    /** 🔢 Tipos de datos automáticos */
    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    /** 🔗 Relaciones Eloquent */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /** ⚙️ Lógica automática: Calcula subtotal antes de guardar */
    protected static function booted()
    {
        static::creating(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });

        static::updating(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });

        // Después de guardar o eliminar, recalcula el total de la Venta.
        static::saved(function ($detalle) {
            $detalle->actualizarTotalVenta();
        });

        static::deleted(function ($detalle) {
            $detalle->actualizarTotalVenta();
        });
    }

    /** 🔄 Método auxiliar para recalcular el total, subtotal e IGV de la venta */
    public function actualizarTotalVenta()
    {
        if ($this->venta) {
            // ✅ CORRECCIÓN: Llamamos al método que calcula el Subtotal, IGV y Total
            // según la lógica del 18% definida en el modelo Venta.
            $this->venta->recalcularTotales(); 
        }
    }
}