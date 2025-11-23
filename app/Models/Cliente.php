<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'documento', // DNI o RUC
        'celular',
        'email',
        'direccion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    // =========================
    // RELACIONES
    // =========================
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class);
    }

    // =========================
    // SCOPES
    // =========================
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeConDeudas($query)
    {
        return $query->whereHas('ventas', function($q) {
            $q->where('tipo_pago', 'credito')->where('pagado', false);
        });
    }

    public function scopeBuscar($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
              ->orWhere('documento', 'like', "%{$term}%");
        });
    }

    public function scopeFacturas($query)
    {
        return $query->whereRaw('LENGTH(documento) = 11');
    }

    public function scopeBoletas($query)
    {
        return $query->whereRaw('LENGTH(documento) = 8');
    }

    // =========================
    // HELPERS
    // =========================
    public function esFactura()
    {
        return strlen($this->documento) === 11;
    }

    public function esBoleta()
    {
        return strlen($this->documento) === 8;
    }

    public function getTotalComprasAttribute()
    {
        return $this->ventas()->sum('total');
    }

    public function getTotalDeudaAttribute()
    {
        return $this->ventas()
            ->where('tipo_pago', 'credito')
            ->where('pagado', false)
            ->sum('total');
    }

    public function tieneDeudas()
    {
        return $this->ventas()
            ->where('tipo_pago', 'credito')
            ->where('pagado', false)
            ->exists();
    }

    public function getCantidadComprasAttribute()
    {
        return $this->ventas()->count();
    }

    // Total de compras por tipo de comprobante
    public function totalComprasPorTipo($tipo = null)
    {
        $query = $this->ventas();
        if ($tipo === 'factura') $query->whereHas('cliente', fn($q) => $q->whereRaw('LENGTH(documento)=11'));
        if ($tipo === 'boleta') $query->whereHas('cliente', fn($q) => $q->whereRaw('LENGTH(documento)=8'));
        return $query->sum('total');
    }

    // Total de deudas por tipo de comprobante
    public function totalDeudaPorTipo($tipo = null)
    {
        $query = $this->ventas()->where('tipo_pago','credito')->where('pagado', false);
        if ($tipo === 'factura') $query->whereHas('cliente', fn($q) => $q->whereRaw('LENGTH(documento)=11'));
        if ($tipo === 'boleta') $query->whereHas('cliente', fn($q) => $q->whereRaw('LENGTH(documento)=8'));
        return $query->sum('total');
    }
}
