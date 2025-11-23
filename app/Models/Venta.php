<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'stand_id',
        'tipo_comprobante',  
        'numero_comprobante',
        'tipo_pago',         // contado o credito
        'metodo_pago',       // efectivo, transferencia, tarjeta
        'pagado',
        'fecha_vencimiento',
        'fecha_pago',
        'subtotal',          // Base imponible sin IGV
        'igv',               // Monto del IGV (18%)
        'total',             // Total con IGV
        'celular_cliente',   // para crédito
        'monto_cuota',       // monto por cuota si aplica
        'fechas_pago',       // array serializado
        'observaciones',     // Observaciones o metadatos (como Stripe Session ID)
        'metadata',          // ✅ NUEVO: JSON con toda la información del pedido
        'estado_sunat',      // PENDIENTE, ACEPTADO, RECHAZADO
        'hash_sunat',
        'mensaje_sunat',
        'estado_pedido',     // ✅ NUEVO: pendiente, en_preparacion, entregado, completado
    ];

    protected $casts = [
        'cliente_id' => 'integer',
        'user_id' => 'integer',
        'stand_id' => 'integer',
        'pagado' => 'boolean',
        'subtotal' => 'decimal:2', 
        'igv' => 'decimal:2', 
        'total' => 'decimal:2',
        'monto_cuota' => 'decimal:2',
        'fecha_vencimiento' => 'datetime',
        'fecha_pago' => 'datetime',
        'tipo_pago' => 'string',
        'metodo_pago' => 'string',
        'fechas_pago' => 'array',
        'metadata' => 'array', // ✅ NUEVO: Convierte automáticamente JSON ↔ Array
    ];

    // =========================
    // RELACIONES
    // =========================
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function stand()
    {
        return $this->belongsTo(Stand::class); 
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function cuotas()
    {
        return $this->hasMany(CuotaVenta::class, 'venta_id');
    }

    // =========================
    // SCOPES
    // =========================
    public function scopeStand($query, $standId)
    {
        return $query->where('stand_id', $standId);
    }

    public function scopeTipoPago($query, $tipo)
    {
        if ($tipo) return $query->where('tipo_pago', $tipo);
        return $query;
    }

    public function scopeRangoFechas($query, $inicio, $fin)
    {
        if ($inicio) $query->whereDate('created_at', '>=', $inicio);
        if ($fin) $query->whereDate('created_at', '<=', $fin);
        return $query;
    }

    public function scopeBuscarCliente($query, $texto)
    {
        if ($texto) {
            return $query->whereHas('cliente', function ($q) use ($texto) {
                $q->where('nombre', 'like', "%$texto%")
                  ->orWhere('documento', 'like', "%$texto%");
            });
        }
        return $query;
    }

    // ✅ NUEVO: Filtrar ventas e-commerce (sin stand_id)
    public function scopeEcommerce($query)
    {
        return $query->whereNull('stand_id');
    }

    // ✅ NUEVO: Filtrar por estado de pedido
    public function scopeEstadoPedido($query, $estado)
    {
        if ($estado) {
            return $query->where('estado_pedido', $estado);
        }
        return $query;
    }

    // Filtrar por tipo de comprobante (boleta o factura)
    public function scopeComprobante($query, $tipo = null)
    {
        if ($tipo === 'factura') {
            return $query->whereHas('cliente', fn($q) => $q->whereRaw('LENGTH(documento)=11'));
        }
        if ($tipo === 'boleta') {
            return $query->whereHas('cliente', fn($q) => $q->whereRaw('LENGTH(documento)=8'));
        }
        return $query;
    }

    // =========================
    // ACCESSORS
    // =========================
    public function getTotalIngresosAttribute()
    {
        return $this->detalles()->sum('subtotal');
    }

    public function getCantidadProductosAttribute()
    {
        return $this->detalles()->sum('cantidad');
    }

    public function getNombreComprobanteAttribute()
    {
        if ($this->numero_comprobante) {
            return $this->tipo_comprobante . ' ' . $this->numero_comprobante;
        }
        return $this->tipo_comprobante . ' (Sin emitir)';
    }

    // ✅ NUEVO: Obtener estado del pedido formateado
    public function getEstadoPedidoTextoAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'en_preparacion' => 'En Preparación',
            'entregado' => 'Entregado',
            'completado' => 'Completado'
        ];
        
        return $estados[$this->estado_pedido] ?? 'Pendiente';
    }

    // ✅ NUEVO: Badge color para el estado
    public function getEstadoPedidoBadgeAttribute()
    {
        $badges = [
            'pendiente' => 'warning',
            'en_preparacion' => 'info',
            'entregado' => 'success',
            'completado' => 'primary'
        ];
        
        return $badges[$this->estado_pedido] ?? 'secondary';
    }

    // ✅ NUEVO: Helpers para acceder a datos de metadata
    public function getStripeSessionIdAttribute()
    {
        return $this->metadata['stripe_session_id'] ?? null;
    }

    public function getStripePaymentIntentAttribute()
    {
        return $this->metadata['stripe_payment_intent'] ?? null;
    }

    public function getCustomerDataAttribute()
    {
        return $this->metadata['customer_data'] ?? null;
    }

    public function getCartDataAttribute()
    {
        return $this->metadata['cart_data'] ?? null;
    }

    // =========================
    // MÉTODOS AUXILIARES
    // =========================
    
    public function recalcularTotales()
    {
        $totalConIGV = $this->detalles()->sum('subtotal');
        $subtotalSinIGV = round($totalConIGV / 1.18, 2);
        $igv = round($totalConIGV - $subtotalSinIGV, 2);

        $this->update([
            'subtotal' => $subtotalSinIGV,
            'igv' => $igv,
            'total' => $totalConIGV
        ]);
    }

    public function actualizarTotal()
    {
        $this->recalcularTotales();
    }

    public function estaPagada()
    {
        if ($this->esContado()) return $this->pagado === true;
        
        if ($this->esCredito()) {
            return $this->cuotas()->count() > 0 && $this->cuotas()->where('pagada', false)->count() === 0;
        }
        return false;
    }

    public function tipoPagoTexto()
    {
        return ucfirst($this->tipo_pago ?? 'Contado');
    }

    public function metodoPagoTexto()
    {
        return ucfirst($this->metodo_pago ?? 'Efectivo');
    }

    public function esCredito()
    {
        return $this->tipo_pago === 'credito';
    }

    public function esContado()
    {
        return $this->tipo_pago === 'contado';
    }

    public function comprobanteEmitido()
    {
        return $this->estado_sunat === 'ACEPTADO' && !empty($this->numero_comprobante);
    }

    // ✅ NUEVO: Verificar si es venta e-commerce
    public function esEcommerce()
    {
        return $this->stand_id === null;
    }

    // ✅ NUEVO: Marcar como atendido
    public function marcarComoAtendido()
    {
        $this->update(['estado_pedido' => 'entregado']);
    }

    // ✅ NUEVO: Marcar como pendiente
    public function marcarComoPendiente()
    {
        $this->update(['estado_pedido' => 'pendiente']);
    }
}