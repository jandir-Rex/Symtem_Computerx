@extends('layouts.appe')

@section('title', 'Detalle del Pedido #' . $venta->id . ' - Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-2">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Pedido #{{ $venta->id }}
                    </h1>
                    <p class="text-muted mb-0">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ $venta->created_at->format('d/m/Y H:i A') }}
                    </p>
                </div>
                <div class="text-end">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
            </div>

            <!-- ESTADO DEL PEDIDO -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-3">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Estado del Pedido
                            </h5>
                            <div class="d-flex flex-wrap gap-2">
                                @if($venta->pagado)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Pagado
                                    </span>
                                @else
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="fas fa-clock me-1"></i>
                                        Pendiente de Pago
                                    </span>
                                @endif

                                @php
                                    $estadoPedido = strtolower($venta->estado_pedido ?? 'pendiente');
                                    $estadosConfig = [
                                        'pendiente' => ['text' => 'Pendiente', 'class' => 'secondary', 'icon' => 'hourglass-half'],
                                        'procesando' => ['text' => 'En Proceso', 'class' => 'info', 'icon' => 'cog'],
                                        'enviado' => ['text' => 'Enviado', 'class' => 'primary', 'icon' => 'shipping-fast'],
                                        'completado' => ['text' => 'Completado', 'class' => 'success', 'icon' => 'check-circle'],
                                        'cancelado' => ['text' => 'Cancelado', 'class' => 'danger', 'icon' => 'times-circle'],
                                    ];
                                    $estadoConfig = $estadosConfig[$estadoPedido] ?? ['text' => 'Procesando', 'class' => 'info', 'icon' => 'cog'];
                                @endphp

                                <span class="badge bg-{{ $estadoConfig['class'] }} px-3 py-2">
                                    <i class="fas fa-{{ $estadoConfig['icon'] }} me-1"></i>
                                    {{ $estadoConfig['text'] }}
                                </span>

                                @if($venta->numero_comprobante)
                                    <span class="badge bg-dark px-3 py-2">
                                        <i class="fas fa-file-invoice me-1"></i>
                                        {{ $venta->numero_comprobante }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($venta->pagado && $venta->numero_comprobante)
                                <a href="{{ route('checkout.descargarComprobante', ['ventaId' => $venta->id]) }}" 
                                   class="btn btn-warning"
                                   target="_blank">
                                    <i class="fas fa-file-pdf me-2"></i>
                                    Descargar PDF
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- INFORMACIÓN DEL CLIENTE -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user me-2"></i>
                                Información del Cliente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Nombre:</small>
                                <p class="mb-0 fw-bold">{{ $venta->cliente->nombre ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Email:</small>
                                <p class="mb-0">
                                    <i class="fas fa-envelope text-primary me-1"></i>
                                    {{ $venta->cliente->email ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Teléfono:</small>
                                <p class="mb-0">
                                    <i class="fas fa-phone text-success me-1"></i>
                                    {{ $venta->cliente->telefono ?? ($venta->celular_cliente ?? 'N/A') }}
                                </p>
                            </div>
                            @if($venta->cliente->documento)
                                <div class="mb-3">
                                    <small class="text-muted">{{ strlen($venta->cliente->documento) === 11 ? 'RUC' : 'DNI' }}:</small>
                                    <p class="mb-0">
                                        <i class="fas fa-id-card text-info me-1"></i>
                                        {{ $venta->cliente->documento }}
                                    </p>
                                </div>
                            @endif
                            @if($venta->cliente->direccion)
                                <div>
                                    <small class="text-muted">Dirección de envío:</small>
                                    <p class="mb-0">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        {{ $venta->cliente->direccion }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- INFORMACIÓN DE PAGO -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-credit-card me-2"></i>
                                Información de Pago
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-muted">Método de Pago:</small>
                                <p class="mb-0 fw-bold">
                                    @php
                                        $metodoPago = strtolower($venta->metodo_pago ?? 'efectivo');
                                        $metodos = [
                                            'tarjeta' => ['icon' => 'credit-card', 'text' => 'Tarjeta de Crédito/Débito'],
                                            'efectivo' => ['icon' => 'money-bill-wave', 'text' => 'Efectivo'],
                                            'transferencia' => ['icon' => 'exchange-alt', 'text' => 'Transferencia Bancaria'],
                                            'yape' => ['icon' => 'mobile-alt', 'text' => 'Yape'],
                                            'plin' => ['icon' => 'mobile-alt', 'text' => 'Plin'],
                                            'delivery' => ['icon' => 'truck', 'text' => 'Pago contra entrega'],
                                        ];
                                        $metodoConfig = $metodos[$metodoPago] ?? ['icon' => 'money-bill', 'text' => ucfirst($metodoPago)];
                                    @endphp
                                    <i class="fas fa-{{ $metodoConfig['icon'] }} me-1"></i>
                                    {{ $metodoConfig['text'] }}
                                </p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Tipo de Pago:</small>
                                <p class="mb-0">{{ ucfirst($venta->tipo_pago ?? 'Contado') }}</p>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Tipo de Comprobante:</small>
                                <p class="mb-0">{{ strtoupper($venta->tipo_comprobante ?? 'Boleta') }}</p>
                            </div>

                            @if($venta->fecha_pago)
                                <div class="mb-3">
                                    <small class="text-muted">Fecha de Pago:</small>
                                    <p class="mb-0">
                                        <i class="far fa-calendar-check text-success me-1"></i>
                                        {{ \Carbon\Carbon::parse($venta->fecha_pago)->format('d/m/Y H:i A') }}
                                    </p>
                                </div>
                            @endif

                            @if($venta->observaciones)
                                <div>
                                    <small class="text-muted">Observaciones:</small>
                                    <p class="mb-0 small text-secondary">{{ $venta->observaciones }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRODUCTOS DEL PEDIDO -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>
                        Productos ({{ $venta->detalles->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 100px;">Imagen</th>
                                    <th>Producto</th>
                                    <th class="text-center" style="width: 120px;">Cantidad</th>
                                    <th class="text-end" style="width: 150px;">Precio Unit.</th>
                                    <th class="text-end" style="width: 150px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalles as $detalle)
                                    <tr>
                                        <td>
                                            @if($detalle->producto)
                                                @php
                                                    $imageUrl = $detalle->producto->imagen_url ?? null;
                                                    if ($imageUrl && str_starts_with($imageUrl, 'http')) {
                                                        $imageUrl = parse_url($imageUrl, PHP_URL_PATH);
                                                        $imageUrl = ltrim($imageUrl, '/');
                                                    }
                                                @endphp
                                                
                                                @if($imageUrl)
                                                    <img src="{{ asset($imageUrl) }}" 
                                                        alt="{{ $detalle->producto->nombre ?? 'Producto' }}"
                                                        class="rounded border"
                                                        style="width: 70px; height: 70px; object-fit: cover;"
                                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="d-none align-items-center justify-content-center bg-light rounded border"
                                                         style="width: 70px; height: 70px;">
                                                        <i class="fas fa-laptop text-muted"></i>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center justify-content-center bg-light rounded border"
                                                         style="width: 70px; height: 70px;">
                                                        <i class="fas fa-laptop text-muted"></i>
                                                    </div>
                                                @endif
                                            @else
                                                <div class="d-flex align-items-center justify-content-center bg-light rounded border"
                                                     style="width: 70px; height: 70px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <h6 class="mb-1">{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</h6>
                                            @if($detalle->producto && $detalle->producto->codigo)
                                                <small class="text-muted">
                                                    <i class="fas fa-barcode me-1"></i>
                                                    {{ $detalle->producto->codigo }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-secondary px-3 py-2">
                                                {{ $detalle->cantidad }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <strong>S/{{ number_format($detalle->precio_unitario, 2) }}</strong>
                                        </td>
                                        <td class="align-middle text-end">
                                            <strong class="text-primary">S/{{ number_format($detalle->subtotal, 2) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RESUMEN DE TOTALES -->
            <div class="row justify-content-end">
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="mb-4">
                                <i class="fas fa-calculator text-primary me-2"></i>
                                Resumen del Pedido
                            </h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <strong>S/{{ number_format($venta->subtotal, 2) }}</strong>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">IGV (18%):</span>
                                <strong>S/{{ number_format($venta->igv, 2) }}</strong>
                            </div>
                            
                            <div class="border-top pt-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0 fw-bold">Total:</span>
                                    <span class="h4 mb-0 text-success fw-bold">S/{{ number_format($venta->total, 2) }}</span>
                                </div>
                            </div>

                            @if($venta->pagado)
                                <div class="alert alert-success mb-0 text-center">
                                    <i class="fas fa-check-circle me-1"></i>
                                    <strong>Pago confirmado</strong>
                                </div>
                            @else
                                <div class="alert alert-warning mb-0 text-center">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    <strong>Pendiente de pago</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="text-center mt-4">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-lg px-5">
                    <i class="fas fa-arrow-left me-2"></i>
                    Volver a Mis Pedidos
                </a>
                
                @if($venta->pagado && $venta->numero_comprobante)
                    <a href="{{ route('checkout.descargarComprobante', ['ventaId' => $venta->id]) }}" 
                       class="btn btn-warning btn-lg px-5 ms-2"
                       target="_blank">
                        <i class="fas fa-download me-2"></i>
                        Descargar Comprobante
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 10px;
    overflow: hidden;
}

.table tbody tr {
    transition: background-color 0.2s;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
}
</style>
@endsection