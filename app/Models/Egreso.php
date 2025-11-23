<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para registrar Compras, Gastos operativos y cualquier egreso
 * que genere Crédito Fiscal (IGV a favor).
 */
class Egreso extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'total', // Monto total con IGV incluido
        'fecha_emision',
        'proveedor_id',
        'documento_tipo', // Factura, Boleta, etc.
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
        'fecha_emision' => 'date',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
}
