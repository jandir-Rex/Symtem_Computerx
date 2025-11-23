<?php
// app/Models/Stand.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stand extends Model
{
    protected $fillable = [
        'nombre',
        'ubicacion',
        'tipo',
        'activo',
        'descripcion'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}