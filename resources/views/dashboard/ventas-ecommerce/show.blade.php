@extends('layouts.dashboard')

@section('title', 'Detalle Venta E-commerce')

@section('content')
<div class="container-fluid px-4">
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="fas fa-shopping-cart text-primary"></i> 
                Detalle de Venta E-commerce
            </h4>
            <small class="text-muted">Código: <strong>ECOM-{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</strong></small>
        </div>
        <div>
            <a href="{{ route('dashboard.ventas-ecommerce.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- COLUMNA IZQUIERDA: INFO GENERAL Y CLIENTE --}}
        <div class="col-lg-4">
            {{-- ESTADO Y MÉTODO --}}
            <div class="card shadow-sm mb-3">
                <div class="card-header py-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-primary"></i> Estado de la Venta
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Estado de Pago</small>
                        @if($venta->pagado)
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle"></i> Pagado
                            </span>
                        @else
                            <span class="badge bg-warning px-3 py-2">
                                <i class="fas fa-clock"></i> Pendiente de Pago
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Método de Pago</small>
                        <span class="badge bg-info px-3 py-2">
                            <i class="fas fa-credit-card"></i> {{ ucfirst($venta->metodo_pago) }}
                        </span>
                    </div>

                    <hr class="my-3">

                    <div class="mb-2">
                        <small class="text-muted">Fecha de Compra</small>
                        <p class="mb-0 fw-bold">{{ $venta->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Tipo de Comprobante</small>
                        <p class="mb-0 fw-bold">{{ $venta->tipo_comprobante ?? 'Boleta' }}</p>
                    </div>

                    @if($venta->usuario)
                    <div class="mb-0">
                        <small class="text-muted">Procesado por</small>
                        <p class="mb-0 fw-bold">{{ $venta->usuario->name }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- INFORMACIÓN DEL CLIENTE --}}
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user text-primary"></i> Datos del Cliente
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="mb-2">
                        <i class="fas fa-user text-muted me-2"></i>
                        <strong>{{ $venta->cliente->nombre }}</strong>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-id-card text-muted me-2"></i>
                        <span class="small">{{ $venta->cliente->documento }}</span>
                    </div>
                    @if($venta->cliente->email)
                    <div class="mb-2">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <span class="small">{{ $venta->cliente->email }}</span>
                    </div>
                    @endif
                    @if($venta->cliente->telefono)
                    <div class="mb-2">
                        <i class="fas fa-phone text-muted me-2"></i>
                        <span class="small">{{ $venta->cliente->telefono }}</span>
                    </div>
                    @endif
                    @if($venta->cliente->direccion)
                    <div class="mb-0">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        <span class="small">{{ $venta->cliente->direccion }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: PRODUCTOS Y TOTALES --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-shopping-bag text-primary"></i> Productos Comprados
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center" width="100">Cantidad</th>
                                    <th class="text-end" width="120">Precio Unit.</th>
                                    <th class="text-end" width="120">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalles as $detalle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($detalle->producto && $detalle->producto->imagen)
                                                <img src="{{ asset('storage/' . $detalle->producto->imagen) }}" 
                                                     width="50" height="50" class="rounded me-3" 
                                                     style="object-fit: cover;"
                                                     onerror="this.src='https://via.placeholder.com/50'">
                                            @else
                                                <div class="bg-light border rounded me-3 d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px; min-width: 50px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong class="d-block">{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</strong>
                                                @if($detalle->producto && $detalle->producto->codigo_barras)
                                                <small class="text-muted">SKU: {{ $detalle->producto->codigo_barras }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $detalle->cantidad }}</span>
                                    </td>
                                    <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="text-end">
                                        <strong>S/ {{ number_format($detalle->subtotal, 2) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- TOTALES --}}
                    <div class="border-top p-3 bg-light">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Subtotal:</span>
                                    <strong>S/ {{ number_format($venta->subtotal, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">IGV (18%):</span>
                                    <strong>S/ {{ number_format($venta->igv, 2) }}</strong>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="h5 mb-0">TOTAL:</span>
                                    <span class="h5 mb-0 text-success">S/ {{ number_format($venta->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- OBSERVACIONES --}}
            @if($venta->observaciones)
            <div class="card shadow-sm mt-3">
                <div class="card-header py-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-sticky-note text-primary"></i> Observaciones
                    </h6>
                </div>
                <div class="card-body py-3">
                    <p class="mb-0 small text-muted">{{ $venta->observaciones }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ACCIONES RÁPIDAS --}}
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-cogs text-primary"></i> Acciones
                    </h6>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex gap-2 flex-wrap">
                        @if(!$venta->pagado)
                        <button class="btn btn-sm btn-success marcar-pagado" 
                                data-id="{{ $venta->id }}">
                            <i class="fas fa-check-circle"></i> Marcar como Pagado
                        </button>
                        <form action="{{ route('dashboard.ventas-ecommerce.destroy', $venta->id) }}" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('⚠️ ¿Eliminar esta venta?\n\nEl stock de los productos será restaurado.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash-alt"></i> Eliminar Venta
                            </button>
                        </form>
                        @else
                        <div class="alert alert-success mb-0 py-2 px-3 d-inline-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <small><strong>Venta completada y pagada</strong></small>
                        </div>
                        @endif
                        
                        <a href="{{ route('dashboard.ventas-ecommerce.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Marcar como pagado (AJAX)
document.querySelectorAll('.marcar-pagado').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('¿Confirmar que esta venta ha sido pagada?')) return;
        
        const id = this.dataset.id;
        const button = this;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        
        fetch(`/dashboard/ventas-ecommerce/${id}/marcar-pagado`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-check-circle"></i> Marcar como Pagado';
            }
        })
        .catch(err => {
            alert('Error al procesar la solicitud');
            console.error(err);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-check-circle"></i> Marcar como Pagado';
        });
    });
});
</script>
@endpush
@endsection