@extends('layouts.appe')

@section('title', 'Mis Pedidos - Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">
                    <i class="fas fa-shopping-bag text-primary me-2"></i>
                    Mis Pedidos
                </h1>
                <p class="lead text-muted">Historial completo de tus compras</p>
            </div>

            @if($ventas->isEmpty())
                <!-- SIN PEDIDOS -->
                <div class="card shadow-sm border-0 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-box-open text-muted mb-3" style="font-size: 5rem;"></i>
                        <h3 class="mb-3">Aún no tienes pedidos</h3>
                        <p class="text-muted mb-4">
                            Cuando hagas una compra, aparecerá aquí
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-home me-2"></i>
                            Ir a la Tienda
                        </a>
                    </div>
                </div>
            @else
                <!-- LISTA DE PEDIDOS -->
                <div class="row g-4">
                    @foreach($ventas as $venta)
                        <div class="col-12">
                            <div class="card shadow-sm border-0 hover-lift">
                                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Pedido #{{ $venta->id }}</strong>
                                        <span class="ms-3 text-white-50">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $venta->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    @php
                                        // Determinar estado basado en estado_pedido y pagado
                                        $estadoPedido = strtolower($venta->estado_pedido ?? 'pendiente');
                                        
                                        if ($venta->pagado) {
                                            if ($estadoPedido === 'completado' || $estadoPedido === 'entregado') {
                                                $badge = ['text' => 'Completado', 'class' => 'success'];
                                            } elseif ($estadoPedido === 'enviado' || $estadoPedido === 'en_camino') {
                                                $badge = ['text' => 'Enviado', 'class' => 'info'];
                                            } else {
                                                $badge = ['text' => 'Pagado - En Proceso', 'class' => 'success'];
                                            }
                                        } else {
                                            $badge = ['text' => 'Pendiente de Pago', 'class' => 'warning'];
                                        }
                                    @endphp
                                    <span class="badge bg-{{ $badge['class'] }}">
                                        {{ $badge['text'] }}
                                    </span>
                                </div>

                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <!-- PRODUCTOS -->
                                        <div class="col-md-6">
                                            <h6 class="mb-3">
                                                <i class="fas fa-box text-primary me-2"></i>
                                                Productos ({{ $venta->detalles->count() }})
                                            </h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($venta->detalles->take(3) as $item)
                                                    @if($item->producto)
                                                        @php
                                                            $imageUrl = $item->producto->imagen_url ?? null;
                                                            if ($imageUrl && str_starts_with($imageUrl, 'http')) {
                                                                $imageUrl = parse_url($imageUrl, PHP_URL_PATH);
                                                                $imageUrl = ltrim($imageUrl, '/');
                                                            }
                                                        @endphp
                                                        
                                                        @if($imageUrl)
                                                            <img src="{{ asset($imageUrl) }}" 
                                                                alt="{{ $item->producto->nombre ?? 'Producto' }}"
                                                                class="rounded border"
                                                                style="width: 60px; height: 60px; object-fit: cover;"
                                                                title="{{ $item->producto->nombre ?? 'Producto' }}"
                                                                onerror="this.style.display='none';">
                                                        @else
                                                            <div class="d-flex align-items-center justify-content-center bg-light rounded border"
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-laptop text-muted"></i>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach
                                                @if($venta->detalles->count() > 3)
                                                    <div class="d-flex align-items-center justify-content-center bg-light rounded border"
                                                         style="width: 60px; height: 60px;">
                                                        <span class="text-muted fw-bold">
                                                            +{{ $venta->detalles->count() - 3 }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- INFO DE PAGO -->
                                        <div class="col-md-3">
                                            <div class="mb-2">
                                                <small class="text-muted">Comprobante:</small>
                                                <br>
                                                <strong>{{ $venta->numero_comprobante ?? 'Pendiente' }}</strong>
                                            </div>
                                            <div>
                                                <small class="text-muted">Método de Pago:</small>
                                                <br>
                                                @php
                                                    $metodoPago = strtolower($venta->metodo_pago ?? 'efectivo');
                                                    $iconos = [
                                                        'tarjeta' => 'credit-card',
                                                        'efectivo' => 'money-bill',
                                                        'transferencia' => 'exchange-alt',
                                                        'yape' => 'mobile-alt',
                                                        'plin' => 'mobile-alt',
                                                    ];
                                                    $icono = $iconos[$metodoPago] ?? 'money-bill';
                                                @endphp
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-{{ $icono }}"></i>
                                                    {{ ucfirst($venta->metodo_pago ?? 'N/A') }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- TOTAL Y ACCIONES -->
                                        <div class="col-md-3 text-end">
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Total:</small>
                                                <h4 class="text-success mb-0">
                                                    S/{{ number_format($venta->total, 2) }}
                                                </h4>
                                            </div>
                                            <a href="{{ route('orders.show', $venta->id) }}" 
                                               class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-eye me-1"></i>
                                                Ver Detalle
                                            </a>
                                            
                                            @if($venta->pagado && $venta->numero_comprobante)
                                                <a href="{{ route('checkout.descargarComprobante', ['ventaId' => $venta->id]) }}" 
                                                   class="btn btn-outline-warning btn-sm w-100 mt-2"
                                                   target="_blank">
                                                    <i class="fas fa-file-pdf me-1"></i>
                                                    Descargar PDF
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINACIÓN -->
                @if(method_exists($ventas, 'links'))
                    <div class="mt-4">
                        {{ $ventas->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>
@endsection