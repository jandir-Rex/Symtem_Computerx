@extends('layouts.app')

@section('title', 'Listado de Ventas - Stand 1')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="fw-bold text-dark m-0">
            <i class="fas fa-receipt"></i> Listado de Ventas
        </h1>

        <div class="d-flex gap-2">
            {{-- 🔙 Volver al Dashboard --}}
            <a href="{{ route('stands.stand1.dashboard') }}" 
               class="btn btn-outline-secondary fw-semibold d-flex align-items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>

            {{-- ➕ Nueva Venta --}}
            <a href="{{ route('stands.stand1.ventas.pos') }}" 
               class="btn btn-dark fw-semibold d-flex align-items-center gap-2 shadow-sm">
                <i class="fas fa-plus-circle"></i> Nueva Venta
            </a>
        </div>
    </div>

    {{-- 🔍 FILTROS --}}
    <form id="formFiltros" method="GET" action="{{ route('stands.stand1.ventas.index') }}" class="card shadow-sm border-light mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                {{-- Tipo de Venta --}}
                <div class="col-md-3">
                    <label for="tipo_pago" class="form-label fw-semibold">Tipo de Venta</label>
                    <select name="tipo_pago" id="tipo_pago" class="form-select shadow-sm filtro">
                        <option value="">Todas</option>
                        <option value="contado" {{ request('tipo_pago') == 'contado' ? 'selected' : '' }}>Contado</option>
                        <option value="credito" {{ request('tipo_pago') == 'credito' ? 'selected' : '' }}>Crédito</option>
                    </select>
                </div>

                {{-- Estado Crédito (solo cuando el tipo es crédito) --}}
                <div class="col-md-3" id="filtro_estado_credito" style="display:none;">
                    <label for="estado_credito" class="form-label fw-semibold">Estado Crédito</label>
                    <select name="estado_credito" id="estado_credito" class="form-select shadow-sm filtro">
                        <option value="">Todos</option>
                        <option value="pagado" {{ request('estado_credito') == 'pagado' ? 'selected' : '' }}>Pagadas</option>
                        <option value="pendiente" {{ request('estado_credito') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                    </select>
                </div>

                {{-- Fecha Inicio --}}
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label fw-semibold">Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" 
                           class="form-control shadow-sm filtro" value="{{ request('fecha_inicio') }}">
                </div>

                {{-- Fecha Fin --}}
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label fw-semibold">Hasta</label>
                    <input type="date" name="fecha_fin" id="fecha_fin" 
                           class="form-control shadow-sm filtro" value="{{ request('fecha_fin') }}">
                </div>

                {{-- Buscar Cliente --}}
                <div class="col-md-3">
                    <label for="buscar" class="form-label fw-semibold">Cliente</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="buscar" id="buscar" 
                               class="form-control filtro" placeholder="Nombre o documento"
                               value="{{ request('buscar') }}">
                    </div>
                </div>
            </div>

            {{-- Mantener solo el botón "Limpiar" --}}
            <div class="mt-3 d-flex justify-content-end">
                <a href="{{ route('stands.stand1.ventas.index') }}" 
                   class="btn btn-outline-secondary fw-semibold shadow-sm" id="btnLimpiar">
                    <i class="fas fa-undo"></i> Limpiar
                </a>
            </div>
        </div>
    </form>

    {{-- 📋 LISTADO DE VENTAS --}}
    <div id="tablaVentas" class="card shadow-sm border-light">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Tipo de Venta</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="ventasBody">
                        @forelse ($ventas as $venta)
                            <tr>
                                <td class="text-center fw-bold">{{ $venta->id }}</td>
                                <td>{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</td>
                                <td class="text-center">
                                    @if($venta->tipo_pago === 'contado')
                                        <span class="badge bg-success">Contado</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Crédito</span>
                                    @endif
                                </td>
                                <td class="text-center fw-semibold">S/ {{ number_format($venta->total, 2) }}</td>
                                <td class="text-center">{{ $venta->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    @if($venta->pagado)
                                        <span class="badge bg-success">Pagada</span>
                                    @else
                                        <span class="badge bg-danger">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('stands.stand1.ventas.show', $venta->id) }}" 
                                       class="btn btn-sm btn-outline-primary shadow-sm">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No se encontraron ventas con esos filtros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            <div id="paginacion" class="mt-3 d-flex justify-content-center">
                {{ $ventas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- ✨ Interacciones visuales + búsqueda en tiempo real --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filtros = document.querySelectorAll('.filtro');
    const tablaVentas = document.getElementById('tablaVentas');
    const formFiltros = document.getElementById('formFiltros');
    const btnLimpiar = document.getElementById('btnLimpiar');

    async function buscarVentas(pageUrl = null) {
        const params = new URLSearchParams({
            tipo_pago: document.getElementById('tipo_pago').value,
            estado_credito: document.getElementById('estado_credito').value,
            fecha_inicio: document.getElementById('fecha_inicio').value,
            fecha_fin: document.getElementById('fecha_fin').value,
            buscar: document.getElementById('buscar').value,
        });

        const url = pageUrl ?? "{{ route('stands.stand1.ventas.index') }}";
        const fetchUrl = `${url}?${params.toString()}`;

        console.log('🔍 Buscando:', fetchUrl);

        try {
            const response = await fetch(fetchUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTabla = doc.querySelector('#tablaVentas');
            
            if (newTabla) {
                tablaVentas.innerHTML = newTabla.innerHTML;
                console.log('✅ Tabla actualizada');
            } else {
                console.error('❌ No se encontró #tablaVentas en la respuesta');
            }
        } catch (err) {
            console.error('❌ Error en la búsqueda:', err);
        }
    }

    // Mostrar select de estado cuando el tipo es crédito
    const tipoVentaSelect = document.getElementById('tipo_pago');
    const filtroEstadoCredito = document.getElementById('filtro_estado_credito');

    function toggleEstadoCredito() {
        if (tipoVentaSelect.value === 'credito') {
            filtroEstadoCredito.style.display = 'block';
        } else {
            filtroEstadoCredito.style.display = 'none';
            document.getElementById('estado_credito').value = '';
        }
    }

    tipoVentaSelect.addEventListener('change', () => {
        toggleEstadoCredito();
        buscarVentas(); 
    });

    // Ejecutar al cargar la página
    toggleEstadoCredito();

    // Filtros dinámicos - SIN pasar el evento
    filtros.forEach(el => {
        el.addEventListener('input', () => buscarVentas());
        el.addEventListener('change', () => buscarVentas());
    });

    // Botón limpiar
    btnLimpiar.addEventListener('click', e => {
        e.preventDefault();
        filtros.forEach(el => el.value = '');
        buscarVentas();
        toggleEstadoCredito();
    });

    // Paginación dinámica
    document.addEventListener('click', e => {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            buscarVentas(link.href);
        }
    });
});
</script>
@endsection