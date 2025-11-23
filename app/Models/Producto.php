<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'codigo_barras',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_minimo',
        'activo',
        'destacado',
        'visible_ecommerce',
        'categoria',
        'imagen',
        'marca',
        'garantia_meses',
        'user_id'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'visible_ecommerce' => 'boolean',
        'garantia_meses' => 'integer',
        'user_id' => 'integer'
    ];

    /* 🔗 Relaciones */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function reparaciones()
    {
        return $this->belongsToMany(Reparacion::class, 'reparacion_repuestos')
            ->withPivot('cantidad', 'precio_unitario', 'subtotal')
            ->withTimestamps();
    }

    /* 🔍 Scopes reutilizables */
    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function scopeSinStock($query)
    {
        return $query->where('stock', 0);
    }

    public function scopeDestacado($query)
    {
        return $query->where('destacado', true);
    }

    public function scopeVisibleEcommerce($query)
    {
        return $query->where('visible_ecommerce', true)
                     ->where('activo', true)
                     ->where('stock', '>', 0);
    }

    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeBuscar($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nombre', 'like', "%{$term}%")
              ->orWhere('codigo_barras', 'like', "%{$term}%");
        });
    }

    /* 💡 Accessors */
    public function getMargenGananciaAttribute()
    {
        return $this->precio_compra == 0
            ? 0
            : round((($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100, 2);
    }

    public function getValorInventarioAttribute()
    {
        return $this->stock * $this->precio_compra;
    }

    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            // Usar URL relativa que funciona con cualquier dominio
            return url('storage/' . $this->imagen);
        }
        
        return asset('images/producto-placeholder.png');
    }

    public function getSlugAttribute()
    {
        return \Illuminate\Support\Str::slug($this->nombre);
    }

    /* ⚙️ Funciones auxiliares */
    public function tieneStock($cantidad = 1)
    {
        return $this->stock >= $cantidad;
    }

    public function enStockMinimo()
    {
        return $this->stock <= $this->stock_minimo;
    }

    // 🆕 MAPEO DE CATEGORÍAS BD → RUTAS E-COMMERCE (ACTUALIZADAS)
    public function getCategoriaUrlAttribute()
    {
        $mapeo = [
            'laptops' => 'laptops',
            'pc' => 'pc-gaming',
            'componentes' => 'componentes',
            'perifericos' => 'perifericos',
            'monitores' => 'monitores',
            'consolas' => 'consolas',
            'accesorios' => 'accesorios',
            'repuestos' => 'repuestos'
        ];

        return $mapeo[$this->categoria] ?? 'accesorios';
    }

    public static function getCategorias()
    {
        return [
            'laptops' => 'Laptops',
            'pc' => 'PC Gaming',
            'componentes' => 'Componentes',
            'perifericos' => 'Periféricos',
            'monitores' => 'Monitores',
            'consolas' => 'Consolas',
            'accesorios' => 'Accesorios',
            'repuestos' => 'Repuestos'
        ];
    }

    // 🆕 MAPEO INVERSO: RUTA E-COMMERCE → CATEGORÍA BD (ACTUALIZADO)
    public static function mapearCategoriaUrl($urlCategoria)
    {
        $mapeoInverso = [
            'laptops' => 'laptops',
            'pc-gaming' => 'pc',
            'componentes' => 'componentes',
            'perifericos' => 'perifericos',
            'monitores' => 'monitores',
            'consolas' => 'consolas',
            'accesorios' => 'accesorios',
            'repuestos' => 'repuestos',
        ];

        return $mapeoInverso[$urlCategoria] ?? null;
    }
}