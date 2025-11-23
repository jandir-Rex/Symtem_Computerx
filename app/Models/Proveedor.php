<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'nombre',
        'telefono',
        'email',
    ];
    
    // Si usas el plural 'proveedores' en lugar de 'proveedors' para la tabla,
    // puedes forzar el nombre de la tabla aquí:
    // protected $table = 'proveedores'; 

    /**
     * Relación: Un proveedor puede tener muchos egresos/compras.
     */
    public function egresos()
    {
        return $this->hasMany(Egreso::class);
    }
}