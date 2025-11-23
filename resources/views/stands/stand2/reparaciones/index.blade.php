@extends('layouts.app')

@section('title', 'Reparaciones Stand 2')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="text-primary">Reparaciones</h2>
        <div>
            <a href="{{ route('stands.stand2.dashboard') }}" class="btn btn-secondary me-2">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="{{ route('stands.stand2.reparaciones.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Nueva reparación
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 🔍 Filtros de búsqueda --}}
    <div class="row mb-4 g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" id="busqueda" class="form-control" placeholder="Buscar por cliente, equipo o diagnóstico...">
        </div>
        <div class="col-md-3">
            <select id="filtroEstado" class="form-select">
                <option value="">Filtrar por estado</option>
                <option value="recibido">Recibido</option>
                <option value="diagnosticando">Diagnosticando</option>
                <option value="en_reparacion">En reparación</option>
                <option value="esperando_repuestos">Esperando repuestos</option>
                <option value="listo">Listo</option>
                <option value="entregado">Entregado</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="filtroPrioridad" class="form-select">
                <option value="">Filtrar por prioridad</option>
                <option value="baja">Baja</option>
                <option value="normal">Normal</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
            </select>
        </div>
        <div class="col-md-3 text-md-end text-center">
            <button id="limpiarFiltros" class="btn btn-outline-secondary">
                <i class="fas fa-undo"></i> Limpiar filtros
            </button>
        </div>
    </div>

    <table class="table table-striped table-hover" id="tablaReparaciones">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th>Total</th>
                <th>Fecha ingreso</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reparaciones as $rep)
            <tr>
                <td>{{ $rep->id }}</td>
                <td>{{ $rep->cliente_nombre }}<br>{{ $rep->cliente_telefono }}</td>
                <td>{{ $rep->tipo_equipo }} {{ $rep->marca }} {{ $rep->modelo }}</td>
                <td>
                    @php
                        $estadoClass = match($rep->estado) {
                            'recibido' => 'badge bg-primary',
                            'diagnosticando' => 'badge bg-warning text-dark',
                            'en_reparacion' => 'badge bg-info text-dark',
                            'esperando_repuestos' => 'badge bg-secondary',
                            'listo' => 'badge bg-success',
                            'entregado' => 'badge bg-dark',
                            'cancelado' => 'badge bg-danger',
                            default => 'badge bg-light text-dark'
                        };
                    @endphp
                    <span class="{{ $estadoClass }}">{{ ucfirst(str_replace('_',' ',$rep->estado)) }}</span>
                </td>
                <td>
                    @php
                        $prioridadClass = match($rep->prioridad) {
                            'baja' => 'badge bg-success',
                            'normal' => 'badge bg-primary',
                            'alta' => 'badge bg-warning text-dark',
                            'urgente' => 'badge bg-danger',
                            default => 'badge bg-light text-dark'
                        };
                    @endphp
                    <span class="{{ $prioridadClass }}">{{ ucfirst($rep->prioridad) }}</span>
                </td>
                <td>{{ number_format($rep->costo_total, 2) }}</td>
                <td>{{ $rep->fecha_ingreso?->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('stands.stand2.reparaciones.show', $rep) }}" class="btn btn-info btn-sm mb-1">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('stands.stand2.reparaciones.edit', $rep) }}" class="btn btn-warning btn-sm mb-1">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('stands.stand2.reparaciones.destroy', $rep) }}" method="POST" class="d-inline mb-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar reparación? Esto es solo lógico.')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- 🔥 Script de búsqueda y filtros en tiempo real con botón de limpieza --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('busqueda');
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroPrioridad = document.getElementById('filtroPrioridad');
    const limpiarBtn = document.getElementById('limpiarFiltros');
    const filas = document.querySelectorAll('#tablaReparaciones tbody tr');

    function filtrar() {
        const texto = input.value.toLowerCase();
        const estado = filtroEstado.value.toLowerCase();
        const prioridad = filtroPrioridad.value.toLowerCase();

        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();
            const coincideTexto = contenido.includes(texto);
            const coincideEstado = estado === '' || contenido.includes(estado);
            const coincidePrioridad = prioridad === '' || contenido.includes(prioridad);
            fila.style.display = (coincideTexto && coincideEstado && coincidePrioridad) ? '' : 'none';
        });
    }

    function limpiarFiltros() {
        input.value = '';
        filtroEstado.value = '';
        filtroPrioridad.value = '';
        filas.forEach(f => f.style.display = '');
    }

    input.addEventListener('input', filtrar);
    filtroEstado.addEventListener('change', filtrar);
    filtroPrioridad.addEventListener('change', filtrar);
    limpiarBtn.addEventListener('click', limpiarFiltros);
});
</script>
@endsection
