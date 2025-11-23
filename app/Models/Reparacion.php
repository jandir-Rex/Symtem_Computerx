<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reparacion extends Model
{
    use SoftDeletes;

    // Forzar el nombre correcto de la tabla
    protected $table = 'reparaciones';

    protected $fillable = [
        'stand_id',
        'cliente_nombre',
        'cliente_telefono',
        'tipo_equipo',
        'marca',
        'modelo',
        'numero_serie',
        'problema_reportado',
        'diagnostico',
        'solucion_aplicada',
        'estado',
        'prioridad',
        'costo_mano_obra',
        'costo_repuestos',
        'costo_total',
        'user_id',
        'fecha_ingreso',
        'fecha_estimada_entrega',
        'fecha_entrega_real',
        'notas_internas',
        'calificacion'
    ];

    protected $casts = [
        'stand_id' => 'integer',
        'costo_mano_obra' => 'decimal:2',
        'costo_repuestos' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'user_id' => 'integer',
        'fecha_ingreso' => 'datetime',
        'fecha_estimada_entrega' => 'datetime',
        'fecha_entrega_real' => 'datetime',
        'calificacion' => 'integer'
    ];
}
