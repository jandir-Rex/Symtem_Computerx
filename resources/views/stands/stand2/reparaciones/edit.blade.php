@extends('layouts.app')

@section('title', 'Editar Reparación')

@section('content')
<div class="container">
    <h2 class="text-primary mb-3">Editar Reparación #{{ $reparacion->id }}</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $bloqueado = $reparacion->estado === 'entregado';
    @endphp

    <form action="{{ route('stands.stand2.reparaciones.update', $reparacion) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre del cliente</label>
                <input type="text" name="cliente_nombre" class="form-control"
                    value="{{ $reparacion->cliente_nombre }}" required {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-6 mb-3">
                <label>Teléfono</label>
                <input type="text" name="cliente_telefono" class="form-control"
                    value="{{ $reparacion->cliente_telefono }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-6 mb-3">
                <label>Tipo de equipo</label>
                <input type="text" name="tipo_equipo" class="form-control"
                    value="{{ $reparacion->tipo_equipo }}" required {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-6 mb-3">
                <label>Marca</label>
                <input type="text" name="marca" class="form-control"
                    value="{{ $reparacion->marca }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-6 mb-3">
                <label>Modelo</label>
                <input type="text" name="modelo" class="form-control"
                    value="{{ $reparacion->modelo }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-6 mb-3">
                <label>Número de serie</label>
                <input type="text" name="numero_serie" class="form-control"
                    value="{{ $reparacion->numero_serie }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-12 mb-3">
                <label>Problema reportado</label>
                <textarea name="problema_reportado" class="form-control" {{ $bloqueado ? 'disabled' : '' }}>{{ $reparacion->problema_reportado }}</textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label>Diagnóstico</label>
                <textarea name="diagnostico" class="form-control" {{ $bloqueado ? 'disabled' : '' }}>{{ $reparacion->diagnostico }}</textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label>Solución aplicada</label>
                <textarea name="solucion_aplicada" class="form-control" {{ $bloqueado ? 'disabled' : '' }}>{{ $reparacion->solucion_aplicada }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>Estado</label>
                <select name="estado" class="form-control" required {{ $bloqueado ? 'disabled' : '' }}>
                    <option value="recibido" {{ $reparacion->estado=='recibido'?'selected':'' }}>Recibido</option>
                    <option value="diagnosticando" {{ $reparacion->estado=='diagnosticando'?'selected':'' }}>Diagnosticando</option>
                    <option value="en_reparacion" {{ $reparacion->estado=='en_reparacion'?'selected':'' }}>En reparación</option>
                    <option value="esperando_repuestos" {{ $reparacion->estado=='esperando_repuestos'?'selected':'' }}>Esperando repuestos</option>
                    <option value="listo" {{ $reparacion->estado=='listo'?'selected':'' }}>Listo</option>
                    <option value="entregado" {{ $reparacion->estado=='entregado'?'selected':'' }}>Entregado</option>
                    <option value="cancelado" {{ $reparacion->estado=='cancelado'?'selected':'' }}>Cancelado</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Prioridad</label>
                <select name="prioridad" class="form-control" {{ $bloqueado ? 'disabled' : '' }}>
                    <option value="baja" {{ $reparacion->prioridad=='baja'?'selected':'' }}>Baja</option>
                    <option value="normal" {{ $reparacion->prioridad=='normal'?'selected':'' }}>Normal</option>
                    <option value="alta" {{ $reparacion->prioridad=='alta'?'selected':'' }}>Alta</option>
                    <option value="urgente" {{ $reparacion->prioridad=='urgente'?'selected':'' }}>Urgente</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Costo mano de obra</label>
                <input type="number" step="0.01" min="0" id="costo_mano_obra" name="costo_mano_obra"
                    class="form-control" value="{{ $reparacion->costo_mano_obra }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-4 mb-3">
                <label>Costo repuestos</label>
                <input type="number" step="0.01" min="0" id="costo_repuestos" name="costo_repuestos"
                    class="form-control" value="{{ $reparacion->costo_repuestos }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-4 mb-3">
                <label>Costo total</label>
                <input type="number" step="0.01" min="0" id="costo_total" name="costo_total"
                    class="form-control" value="{{ $reparacion->costo_total }}" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label>Fecha ingreso</label>
                <input type="date" name="fecha_ingreso" class="form-control"
                    value="{{ $reparacion->fecha_ingreso?->format('Y-m-d') }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-6 mb-3">
                <label>Fecha estimada entrega</label>
                <input type="date" name="fecha_estimada_entrega" class="form-control"
                    value="{{ $reparacion->fecha_estimada_entrega?->format('Y-m-d') }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
            <div class="col-md-12 mb-3">
                <label>Notas internas</label>
                <textarea name="notas_internas" class="form-control" {{ $bloqueado ? 'disabled' : '' }}>{{ $reparacion->notas_internas }}</textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label>Calificación (1-5)</label>
                <input type="number" name="calificacion" class="form-control"
                    min="1" max="5" value="{{ $reparacion->calificacion }}" {{ $bloqueado ? 'disabled' : '' }}>
            </div>
        </div>

        @if(!$bloqueado)
            <button type="submit" class="btn btn-primary">Actualizar reparación</button>
        @endif
        <a href="{{ route('stands.stand2.reparaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const manoObra = document.querySelector('#costo_mano_obra');
    const repuestos = document.querySelector('#costo_repuestos');
    const total = document.querySelector('#costo_total');

    function calcularTotal() {
        const m = parseFloat(manoObra?.value) || 0;
        const r = parseFloat(repuestos?.value) || 0;
        if (total) total.value = (m + r).toFixed(2);
    }

    if (manoObra && repuestos) {
        manoObra.addEventListener('input', calcularTotal);
        repuestos.addEventListener('input', calcularTotal);
        calcularTotal();
    }
});
</script>

@endsection
