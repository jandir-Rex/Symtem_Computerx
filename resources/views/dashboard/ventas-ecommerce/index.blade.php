@extends('layouts.dashboard')

@section('title', 'Ventas E-commerce')

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- ENCABEZADO PROFESIONAL --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="fas fa-shopping-cart text-primary me-2"></i>
                Ventas E-commerce
            </h1>
            <p class="text-muted mb-0 small">
                <i class="fas fa-info-circle me-1"></i>
                Gestión de pedidos online
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>

    {{-- ESTADÍSTICAS MEJORADAS --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background: rgba(59, 130, 246, 0.1);">
                                <i class="fas fa-dollar-sign fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Ventas</h6>
                            <h3 class="mb-0 fw-bold">S/ {{ number_format($stats['total_ventas'], 2) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> Este mes
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background: rgba(16, 185, 129, 0.1);">
                                <i class="fas fa-shopping-bag fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Total Pedidos</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['cantidad_ventas']) }}</h3>
                            <small class="text-muted">Procesados</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background: rgba(245, 158, 11, 0.1);">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Pendientes</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['pendientes']) }}</h3>
                            <small class="text-warning">
                                <i class="fas fa-exclamation-circle"></i> Requieren atención
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle p-3" style="background: rgba(34, 197, 94, 0.1);">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 small">Atendidos</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['atendidos']) }}</h3>
                            <small class="text-success">
                                <i class="fas fa-check"></i> Completados
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS PROFESIONALES --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0 fw-bold">
                <i class="fas fa-filter text-primary me-2"></i>
                Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.ventas-ecommerce.index') }}">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Estado del Pedido</label>
                        <select name="estado" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="todos" {{ $estado === 'todos' ? 'selected' : '' }}>
                                📊 Todos
                            </option>
                            <option value="pendiente" {{ $estado === 'pendiente' ? 'selected' : '' }}>
                                📦 Pendientes de Envío
                            </option>
                            <option value="atendido" {{ $estado === 'atendido' ? 'selected' : '' }}>
                                ✅ Atendidos
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Buscar Cliente</label>
                        <input type="text" name="buscar" class="form-control form-control-sm" 
                               placeholder="Nombre, email, DNI..." value="{{ request('buscar') }}">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Fecha Desde</label>
                        <input type="date" name="fecha_inicio" class="form-control form-control-sm" 
                               value="{{ request('fecha_inicio') }}">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-semibold text-muted mb-1">Fecha Hasta</label>
                        <input type="date" name="fecha_fin" class="form-control form-control-sm" 
                               value="{{ request('fecha_fin') }}">
                    </div>

                    <div class="col-lg-2 col-md-12">
                        <label class="form-label small fw-semibold text-muted mb-1">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search me-1"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA PROFESIONAL --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8f9fa;">
                        <tr>
                            <th class="border-0 py-3 px-4 text-muted small fw-semibold">CÓDIGO</th>
                            <th class="border-0 py-3 text-muted small fw-semibold">FECHA</th>
                            <th class="border-0 py-3 text-muted small fw-semibold">CLIENTE</th>
                            <th class="border-0 py-3 text-muted small fw-semibold">CONTACTO</th>
                            <th class="border-0 py-3 text-muted small fw-semibold text-end">TOTAL</th>
                            <th class="border-0 py-3 text-muted small fw-semibold text-center">ESTADO</th>
                            <th class="border-0 py-3 text-muted small fw-semibold text-center">ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr class="border-bottom">
                            <td class="px-4 py-3">
                                <strong class="text-primary">ECOM-{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td class="py-3">
                                <div class="small">{{ $venta->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $venta->created_at->format('H:i') }}</div>
                            </td>
                            <td class="py-3">
                                <div class="fw-semibold">{{ $venta->cliente->nombre }}</div>
                                <div class="text-muted small">{{ $venta->cliente->documento }}</div>
                            </td>
                            <td class="py-3">
                                @if($venta->cliente->email)
                                <div class="small">
                                    <i class="fas fa-envelope text-muted me-1"></i>
                                    {{ $venta->cliente->email }}
                                </div>
                                @endif
                                @if($venta->cliente->telefono)
                                <div class="small text-muted">
                                    <i class="fas fa-phone text-muted me-1"></i>
                                    {{ $venta->cliente->telefono }}
                                </div>
                                @endif
                            </td>
                            <td class="py-3 text-end">
                                <strong class="text-success">S/ {{ number_format($venta->total, 2) }}</strong>
                            </td>
                            <td class="py-3 text-center">
                                @php
                                    $estadoPedido = strtolower($venta->estado_pedido ?? '');
                                    $estadosAtendidos = ['atendido', 'entregado', 'completado', 'aceptado'];
                                    $esAtendido = in_array($estadoPedido, $estadosAtendidos);
                                @endphp
                                
                                @if($esAtendido)
                                    <span class="badge rounded-pill bg-success px-3 py-2">
                                        <i class="fas fa-check me-1"></i> Atendido
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                                        <i class="fas fa-clock me-1"></i> Pendiente
                                    </span>
                                @endif
                                
                                {{-- 🔥 DEBUG TEMPORAL - ELIMINAR DESPUÉS --}}
                                <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                    DB: "{{ $venta->estado_pedido ?? 'NULL' }}"
                                </div>
                            </td>
                            <td class="py-3 text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('dashboard.ventas-ecommerce.show', $venta->id) }}" 
                                       class="btn btn-outline-primary" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @php
                                        $estadoPedido = strtolower($venta->estado_pedido ?? '');
                                        $estadosPendientes = ['pendiente', 'en_preparacion'];
                                        $esPendiente = empty($estadoPedido) || in_array($estadoPedido, $estadosPendientes) || 
                                                      strpos($estadoPedido, 'pendiente') !== false || 
                                                      strpos($estadoPedido, 'preparacion') !== false;
                                    @endphp

                                    @if($esPendiente)
                                        <button class="btn btn-outline-success marcar-atendido" 
                                                data-id="{{ $venta->id }}" title="Marcar como atendido">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-outline-warning marcar-pendiente" 
                                                data-id="{{ $venta->id }}" title="Regresar a pendiente">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif

                                    <button class="btn btn-outline-danger eliminar-venta" 
                                            data-id="{{ $venta->id }}" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                    <h5 class="fw-normal">No hay pedidos {{ $estado === 'pendiente' ? 'pendientes' : ($estado === 'atendido' ? 'atendidos' : '') }}</h5>
                                    <p class="small mb-0">Los nuevos pedidos aparecerán aquí automáticamente</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($ventas->hasPages())
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Mostrando {{ $ventas->firstItem() }} a {{ $ventas->lastItem() }} de {{ $ventas->total() }} pedidos
                </div>
                <div>
                    {{ $ventas->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// ✅ MARCAR COMO ATENDIDO
document.querySelectorAll('.marcar-atendido').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        
        Swal.fire({
            title: '¿Marcar como atendido?',
            text: 'El pedido se marcará como enviado/completado',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, marcar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = this;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                fetch(`/dashboard/ventas-ecommerce/${id}/marcar-atendido`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Listo!', 'Pedido marcado como atendido', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-check"></i>';
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-check"></i>';
                });
            }
        });
    });
});

// ✅ MARCAR COMO PENDIENTE
document.querySelectorAll('.marcar-pendiente').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        
        Swal.fire({
            title: '¿Regresar a pendiente?',
            text: 'El pedido volverá al estado pendiente de envío',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, regresar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const button = this;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                fetch(`/dashboard/ventas-ecommerce/${id}/marcar-pendiente`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('¡Listo!', 'Pedido regresado a pendiente', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message, 'error');
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-undo"></i>';
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-undo"></i>';
                });
            }
        });
    });
});

// ✅ ELIMINAR VENTA
document.querySelectorAll('.eliminar-venta').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        
        Swal.fire({
            title: '¿Eliminar esta venta?',
            text: 'Esta acción no se puede deshacer. El stock será restaurado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/dashboard/ventas-ecommerce/${id}`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
});
</script>

{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@endsection