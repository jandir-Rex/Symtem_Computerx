@extends('layouts.appe')

@section('title', 'Pedido Confirmado - Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-4">
                <div class="success-animation">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
                
                @if(session('success'))
                    <h1 class="display-5 text-success fw-bold mt-4">¡Pedido Confirmado!</h1>
                    <p class="lead text-muted">{{ session('success') }}</p>
                @else
                    <h1 class="display-5 text-success fw-bold mt-4">¡Gracias por tu compra!</h1>
                    <p class="lead text-muted">Tu pedido ha sido procesado exitosamente</p>
                @endif
            </div>

            @if(session('order'))
                @php
                    $order = session('order');
                    $ventaId = $order['id'] ?? null;
                @endphp
                
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>
                            Resumen del Pedido
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                            <i class="fas fa-info-circle fa-2x me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Código de Pedido</h6>
                                <strong class="fs-4">{{ $order['code'] ?? 'PED-'.str_pad($order['id'], 6, '0', STR_PAD_LEFT) }}</strong>
                                <p class="mb-0 small">Guarda este código para rastrear tu pedido</p>
                            </div>
                        </div>
                        
                        {{-- BOTÓN DE DESCARGA DEL COMPROBANTE --}}
                        @if($ventaId)
                        <div class="text-center mb-4">
                            <a href="{{ route('checkout.descargarComprobante', $ventaId) }}" 
                               class="btn btn-warning btn-lg px-5" 
                               id="descargar-comprobante-manual" 
                               target="_blank">
                                <i class="fas fa-file-pdf me-2"></i> Descargar Comprobante
                            </a>
                            <p class="small text-muted mt-2">La descarga debería iniciar automáticamente.</p>
                        </div>
                        @endif
                        {{-- FIN BOTÓN DESCARGA --}}

                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-user text-primary me-2"></i>
                            Información de Envío
                        </h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Nombre:</strong> {{ $order['customer']['name'] ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Email:</strong> {{ $order['customer']['email'] ?? 'N/A' }}</p>
                                <p class="mb-2"><strong>Teléfono:</strong> {{ $order['customer']['phone'] ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        Dirección:
                                    </strong>
                                </p>
                                <p class="mb-0 text-muted">
                                    {{ $order['customer']['address'] ?? 'N/A' }}<br>
                                    {{ $order['customer']['district'] ?? '' }} - {{ $order['customer']['city'] ?? '' }}
                                </p>
                            </div>
                        </div>

                        <h6 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-shopping-bag text-primary me-2"></i>
                            Productos
                        </h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio (Inc. IGV)</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order['items'] as $item)
                                        @php
                                            $cantidad = $item['quantity'] ?? ($item['qty'] ?? 1);
                                            $precio = floatval($item['price'] ?? 0);
                                            $subtotal = $precio * $cantidad;
                                        @endphp
                                        <tr>
                                            <td>
                                                @if(isset($item['image']))
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                                {{ $item['name'] ?? 'Producto' }}
                                            </td>
                                            <td class="text-center">{{ $cantidad }}</td>
                                            <td class="text-end">S/{{ number_format($precio, 2) }}</td>
                                            <td class="text-end fw-bold">S/{{ number_format($subtotal, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row justify-content-end">
                            <div class="col-md-5">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span>S/{{ number_format($order['subtotal'] ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>IGV (18%):</span>
                                            <span>S/{{ number_format($order['igv'] ?? 0, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong class="fs-5">Total:</strong>
                                            <strong class="fs-5 text-success">
                                                S/{{ number_format($order['total'] ?? 0, 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Próximos Pasos
                        </h5>
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li class="mb-2">
                                <strong>Confirmación por Email:</strong> 
                                Recibirás un correo de confirmación en <span class="text-primary">{{ $order['customer']['email'] ?? 'tu email' }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Preparación del Pedido:</strong> 
                                Nuestro equipo comenzará a preparar tu pedido inmediatamente
                            </li>
                            <li class="mb-2">
                                <strong>Envío:</strong> 
                                Te notificaremos cuando tu pedido sea enviado con el código de rastreo
                            </li>
                            <li>
                                <strong>Contacto:</strong> 
                                Si tienes alguna pregunta, llámanos al <strong class="text-danger">948-004-257</strong>
                            </li>
                        </ol>
                    </div>
                </div>
            @else
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h3 class="mt-3">¡Pedido Procesado!</h3>
                        <p class="text-muted">
                            {{ session('info') ?? 'Tu pedido ha sido recibido y está siendo procesado.' }}
                        </p>
                        <p class="small text-muted">
                            Recibirás un email de confirmación con los detalles de tu compra.
                        </p>
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-home me-2"></i>
                    Volver al Inicio
                </a>
                @auth
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-lg px-5 ms-2">
                        <i class="fas fa-list me-2"></i>
                        Mis Pedidos
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- SCRIPTS PARA DESCARGA AUTOMÁTICA DEL COMPROBANTE --}}
@if (session('download_venta_id'))
    <script>
        const ventaId = {{ session('download_venta_id') }};
        const storageKey = 'download_triggered_' + ventaId;
        
        const downloadLink = document.getElementById('descargar-comprobante-manual');

        if (!sessionStorage.getItem(storageKey)) {
            sessionStorage.setItem(storageKey, 'true');
            
            if (downloadLink) {
                console.log('🔽 Iniciando descarga automática para venta:', ventaId);
                downloadLink.click();

                setTimeout(() => {
                    sessionStorage.removeItem(storageKey);
                }, 10000);
            } else {
                console.error('❌ Elemento de descarga manual no encontrado.');
            }
        } else {
            console.log('ℹ️ Descarga ya fue iniciada para venta:', ventaId);
        }
    </script>
@endif
@endpush

@push('styles')
<style>
/* ========================================
   ANIMACIÓN DE ÉXITO - CHECKMARK
======================================== */
.success-animation {
    margin: 0 auto;
    width: 120px;
    height: 120px;
}

.checkmark {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: block;
    stroke-width: 3;
    stroke: #10b981;
    stroke-miterlimit: 10;
    box-shadow: inset 0 0 0 #10b981;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    position: relative;
}

.checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 3;
    stroke-miterlimit: 10;
    stroke: #10b981;
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke: #fff;
    stroke-width: 3;
    stroke-linecap: round;
    stroke-linejoin: round;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: inset 0 0 0 60px #10b981;
    }
}

/* ========================================
   TARJETAS Y EFECTOS
======================================== */
.card {
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    padding: 1.25rem 1.5rem;
}

.btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-lg {
    padding: 0.75rem 2rem;
}

.btn-primary:hover,
.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-outline-secondary:hover {
    transform: translateY(-2px);
}

.table {
    margin-bottom: 0;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.alert {
    border-radius: 10px;
    border: none;
}

/* ========================================
   RESPONSIVE
======================================== */
@media (max-width: 768px) {
    .success-animation {
        width: 100px;
        height: 100px;
    }
    
    .checkmark {
        width: 100px;
        height: 100px;
    }
    
    .display-5 {
        font-size: 2rem;
    }
    
    .btn-lg {
        padding: 0.625rem 1.5rem;
        font-size: 0.95rem;
    }
}
</style>
@endpush
@endsection