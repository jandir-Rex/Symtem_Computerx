@extends('layouts.dashboard')

@section('title', 'Reporte de Ventas - Admin')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">
            <i class="fas fa-chart-bar"></i> Reporte de Ventas por Stand
        </h2>
        <p class="text-muted">Visualiza y filtra las ventas de todos los stands</p>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="row g-4 mb-4" id="statsContainer">
        @if(request('tipo_filtro') === 'reparaciones' && $standSeleccionado == 2)
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <p>Total Reparaciones</p>
                    <h3>{{ $stats['total_reparaciones'] ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <p>Ingresos Totales</p>
                    <h3>S/ {{ number_format($stats['total_ingresos'] ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <p>Pendientes</p>
                    <h3>{{ $stats['pendientes'] ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p>Completadas</p>
                    <h3>{{ $stats['completadas'] ?? 0 }}</h3>
                </div>
            </div>
        @else
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <p>Total Ventas</p>
                    <h3>S/ {{ number_format($stats['total_ventas'] ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <p>Cantidad de Ventas</p>
                    <h3>{{ $stats['cantidad_ventas'] ?? 0 }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <p>Ventas al Contado</p>
                    <h3>S/ {{ number_format($stats['ventas_contado'] ?? 0, 2) }}</h3>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <p>Ventas a Crédito</p>
                    <h3>S/ {{ number_format($stats['ventas_credito'] ?? 0, 2) }}</h3>
                </div>
            </div>
        @endif
    </div>

    {{-- FILTROS --}}
    <form id="formFiltros" method="GET" class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-semibold">Stand</label>
                    <select name="stand" id="stand" class="form-select filtro">
                        <option value="1" {{ $standSeleccionado == 1 ? 'selected' : '' }}>Stand 1</option>
                        <option value="2" {{ $standSeleccionado == 2 ? 'selected' : '' }}>Stand 2</option>
                    </select>
                </div>

                <div class="col-md-2" id="div_tipo_filtro" style="display: {{ $standSeleccionado == 2 ? 'block' : 'none' }};">
                    <label class="form-label fw-semibold">Tipo</label>
                    <select name="tipo_filtro" id="tipo_filtro" class="form-select filtro">
                        <option value="ventas" {{ ($tipoFiltro ?? 'ventas') === 'ventas' ? 'selected' : '' }}>Ventas</option>
                        <option value="reparaciones" {{ ($tipoFiltro ?? '') === 'reparaciones' ? 'selected' : '' }}>Reparaciones</option>
                    </select>
                </div>

                <div id="filtros_ventas" style="display: {{ ($tipoFiltro ?? '') === 'reparaciones' ? 'none' : 'contents' }};">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-select filtro">
                            <option value="">Todos</option>
                            <option value="contado" {{ request('metodo_pago') == 'contado' ? 'selected' : '' }}>Contado</option>
                            <option value="credito" {{ request('metodo_pago') == 'credito' ? 'selected' : '' }}>Crédito</option>
                        </select>
                    </div>

                    <div class="col-md-2" id="div_estado_credito" style="display: {{ request('metodo_pago') == 'credito' ? 'block' : 'none' }};">
                        <label class="form-label fw-semibold">Estado Crédito</label>
                        <select name="estado_credito" id="estado_credito" class="form-select filtro">
                            <option value="">Todos</option>
                            <option value="pagado" {{ request('estado_credito') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                            <option value="pendiente" {{ request('estado_credito') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                    </div>
                </div>

                <div id="filtros_reparaciones" style="display: {{ ($tipoFiltro ?? '') === 'reparaciones' ? 'contents' : 'none' }};">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Estado</label>
                        <select name="estado_reparacion" id="estado_reparacion" class="form-select filtro">
                            <option value="">Todos</option>
                            <option value="recibido" {{ request('estado_reparacion') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                            <option value="en_reparacion" {{ request('estado_reparacion') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                            <option value="listo" {{ request('estado_reparacion') == 'listo' ? 'selected' : '' }}>Listo</option>
                            <option value="entregado" {{ request('estado_reparacion') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control filtro" value="{{ request('fecha_inicio') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control filtro" value="{{ request('fecha_fin') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Buscar</label>
                    <input type="text" name="buscar" id="buscar" class="form-control filtro" placeholder="Cliente, teléfono, equipo..." value="{{ request('buscar') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" id="btnLimpiar" class="btn btn-outline-secondary w-100" title="Limpiar filtros">
                        <i class="fas fa-broom"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- TABLA --}}
    <div id="tablaResultados" class="card shadow-sm">
        <div class="card-body">
            @if(request('tipo_filtro') === 'reparaciones' && $standSeleccionado == 2)
                @if(isset($reparaciones) && $reparaciones->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Equipo</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reparaciones as $rep)
                                    <tr>
                                        <td>{{ $rep->id }}</td>
                                        <td>{{ $rep->cliente_nombre }}</td>
                                        <td>{{ $rep->tipo_equipo }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($rep->estado === 'recibido') bg-secondary
                                                @elseif($rep->estado === 'en_reparacion') bg-warning
                                                @elseif($rep->estado === 'listo') bg-info
                                                @elseif($rep->estado === 'entregado') bg-success
                                                @else bg-secondary
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $rep->estado)) }}
                                            </span>
                                        </td>
                                        <td>S/ {{ number_format($rep->costo_total, 2) }}</td>
                                        <td>{{ $rep->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-ver-reparacion" data-id="{{ $rep->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $reparaciones->links() }}
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron reparaciones</h5>
                        <p class="text-muted">Intenta modificar los filtros de búsqueda</p>
                    </div>
                @endif
            @else
                @if(isset($ventas) && $ventas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventas as $venta)
                                    <tr>
                                        <td>{{ $venta->id }}</td>
                                        <td>{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</td>
                                        <td>
                                            @if($venta->tipo_pago === 'contado')
                                                <span class="badge bg-success">Contado</span>
                                            @else
                                                <span class="badge bg-warning">Crédito</span>
                                            @endif
                                        </td>
                                        <td>S/ {{ number_format($venta->total, 2) }}</td>
                                        <td>{{ $venta->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            @if($venta->pagado)
                                                <span class="badge bg-success">Pagada</span>
                                            @else
                                                <span class="badge bg-danger">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary btn-ver-venta" data-id="{{ $venta->id }}" data-stand="{{ $standSeleccionado }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $ventas->links() }}
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron ventas</h5>
                        <p class="text-muted">Intenta modificar los filtros de búsqueda</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- MODAL DETALLE DE VENTA --}}
<div class="modal fade" id="modalDetalleVenta" tabindex="-1" aria-labelledby="modalDetalleVentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleVentaLabel">
                    <i class="fas fa-receipt"></i> Detalle de la Venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoDetalleVenta">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando información...</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETALLE DE REPARACIÓN --}}
<div class="modal fade" id="modalDetalleReparacion" tabindex="-1" aria-labelledby="modalDetalleReparacionLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleReparacionLabel">
                    <i class="fas fa-wrench"></i> Detalle de la Reparación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoDetalleReparacion">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando información...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const standSelect = document.getElementById('stand');
    const tipoFiltroDiv = document.getElementById('div_tipo_filtro');
    const tipoFiltroSelect = document.getElementById('tipo_filtro');
    const metodoPagoSelect = document.getElementById('metodo_pago');
    const estadoCreditoDiv = document.getElementById('div_estado_credito');
    const filtrosVentas = document.getElementById('filtros_ventas');
    const filtrosReparaciones = document.getElementById('filtros_reparaciones');
    const filtros = document.querySelectorAll('.filtro');
    const btnLimpiar = document.getElementById('btnLimpiar');
    
    let searchTimeout = null;
    const DEBOUNCE_DELAY = 500;

    function actualizarFiltros() {
        const stand = standSelect.value;
        const tipoFiltro = tipoFiltroSelect.value;

        if (stand == 2) {
            tipoFiltroDiv.style.display = 'block';
            if (tipoFiltro === 'reparaciones') {
                filtrosVentas.style.display = 'none';
                filtrosReparaciones.style.display = 'contents';
            } else {
                filtrosVentas.style.display = 'contents';
                filtrosReparaciones.style.display = 'none';
            }
        } else {
            tipoFiltroDiv.style.display = 'none';
            filtrosVentas.style.display = 'contents';
            filtrosReparaciones.style.display = 'none';
        }

        if (metodoPagoSelect.value === 'credito') {
            estadoCreditoDiv.style.display = 'block';
        } else {
            estadoCreditoDiv.style.display = 'none';
        }
    }

    async function buscarResultados() {
        actualizarFiltros();
        
        const tablaResultados = document.getElementById('tablaResultados');
        const statsContainer = document.getElementById('statsContainer');
        const cardBody = tablaResultados.querySelector('.card-body');
        
        cardBody.style.opacity = '0.5';
        cardBody.style.pointerEvents = 'none';
        
        const params = new URLSearchParams();
        filtros.forEach(filtro => {
            if (filtro.value) params.append(filtro.name, filtro.value);
        });

        const url = "{{ route('dashboard.ventas.index') }}?" + params.toString();
        
        try {
            const response = await fetch(url, { 
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if (!response.ok) throw new Error('Error en la petición');
            
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            const newTabla = doc.querySelector('#tablaResultados');
            if (newTabla) {
                tablaResultados.innerHTML = newTabla.innerHTML;
                inicializarBotonesVer();
            }
            
            const newStats = doc.querySelector('#statsContainer');
            if (newStats) {
                statsContainer.innerHTML = newStats.innerHTML;
            }
            
        } catch (err) {
            console.error('Error:', err);
            cardBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Error al cargar los datos. Por favor, intenta nuevamente.
                </div>
            `;
        } finally {
            cardBody.style.opacity = '1';
            cardBody.style.pointerEvents = 'auto';
        }
    }

    function buscarConDebounce() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(buscarResultados, DEBOUNCE_DELAY);
    }

    standSelect.addEventListener('change', buscarResultados);
    tipoFiltroSelect.addEventListener('change', buscarResultados);
    metodoPagoSelect.addEventListener('change', buscarResultados);
    
    filtros.forEach(filtro => {
        if (filtro.type === 'text' || filtro.type === 'search') {
            filtro.addEventListener('input', buscarConDebounce);
        } else {
            filtro.addEventListener('change', buscarResultados);
        }
    });

    btnLimpiar.addEventListener('click', () => {
        filtros.forEach(f => {
            if (f.id !== 'stand') f.value = '';
        });
        clearTimeout(searchTimeout);
        buscarResultados();
    });

    // 🆕 FUNCIÓN PARA VER DETALLE DE VENTA
    function inicializarBotonesVer() {
        // Botones de ventas
        document.querySelectorAll('.btn-ver-venta').forEach(btn => {
            btn.addEventListener('click', async () => {
                const ventaId = btn.dataset.id;
                const stand = btn.dataset.stand;
                const modal = new bootstrap.Modal(document.getElementById('modalDetalleVenta'));
                const contenido = document.getElementById('contenidoDetalleVenta');
                
                modal.show();
                contenido.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
                
                try {
                    const url = stand == 1 
                        ? `/stand1/ventas/${ventaId}/detalle`
                        : `/stand2/ventas/${ventaId}/detalle`;
                    
                    const response = await fetch(url);
                    const html = await response.text();
                    contenido.innerHTML = html;
                } catch (error) {
                    contenido.innerHTML = '<div class="alert alert-danger">Error al cargar el detalle</div>';
                }
            });
        });

        // Botones de reparaciones
        document.querySelectorAll('.btn-ver-reparacion').forEach(btn => {
            btn.addEventListener('click', async () => {
                const repId = btn.dataset.id;
                const modal = new bootstrap.Modal(document.getElementById('modalDetalleReparacion'));
                const contenido = document.getElementById('contenidoDetalleReparacion');
                
                modal.show();
                contenido.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
                
                try {
                    const response = await fetch(`/stand2/reparaciones/${repId}/detalle`);
                    const html = await response.text();
                    contenido.innerHTML = html;
                } catch (error) {
                    contenido.innerHTML = '<div class="alert alert-danger">Error al cargar el detalle</div>';
                }
            });
        });
    }

    inicializarBotonesVer();
    actualizarFiltros();
});
</script>
@endpush
@endsection