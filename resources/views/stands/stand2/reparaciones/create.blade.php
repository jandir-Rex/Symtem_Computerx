@extends('layouts.app')

@section('title', 'Registrar Reparación')

@section('content')
<div class="container">
    <h2 class="text-primary mb-3">Registrar Nueva Reparación</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stands.stand2.reparaciones.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nombre del cliente</label>
                <input type="text" name="cliente_nombre" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Teléfono</label>
                <input type="text" name="cliente_telefono" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Tipo de equipo</label>
                <input type="text" name="tipo_equipo" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Marca</label>
                <input type="text" name="marca" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Modelo</label>
                <input type="text" name="modelo" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Número de serie</label>
                <input type="text" name="numero_serie" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>Problema reportado</label>
                <textarea name="problema_reportado" class="form-control"></textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label>Diagnóstico</label>
                <textarea name="diagnostico" class="form-control"></textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label>Solución aplicada</label>
                <textarea name="solucion_aplicada" class="form-control"></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label>Estado</label>
                <select name="estado" class="form-control" required>
                    <option value="recibido">Recibido</option>
                    <option value="diagnosticando">Diagnosticando</option>
                    <option value="en_reparacion">En reparación</option>
                    <option value="esperando_repuestos">Esperando repuestos</option>
                    <option value="listo">Listo</option>
                    <option value="entregado">Entregado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Prioridad</label>
                <select name="prioridad" class="form-control">
                    <option value="baja">Baja</option>
                    <option value="normal" selected>Normal</option>
                    <option value="alta">Alta</option>
                    <option value="urgente">Urgente</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Costo mano de obra</label>
                <input type="number" step="0.01" min="0" id="costo_mano_obra" name="costo_mano_obra" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Costo repuestos</label>
                <input type="number" step="0.01" min="0" id="costo_repuestos" name="costo_repuestos" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label>Costo total</label>
                <input type="number" step="0.01" min="0" id="costo_total" name="costo_total" class="form-control" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label>Fecha ingreso</label>
                <input type="date" name="fecha_ingreso" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Fecha estimada entrega</label>
                <input type="date" name="fecha_estimada_entrega" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label>Notas internas</label>
                <textarea name="notas_internas" class="form-control"></textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label>Calificación (1-5)</label>
                <input type="number" name="calificacion" class="form-control" min="1" max="5">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar reparación</button>
        <a href="{{ route('stands.stand2.reparaciones.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    const manoObra = document.getElementById('costo_mano_obra');
    const repuestos = document.getElementById('costo_repuestos');
    const total = document.getElementById('costo_total');

    function calcularTotal() {
        const m = parseFloat(manoObra.value) || 0;
        const r = parseFloat(repuestos.value) || 0;
        total.value = (m + r).toFixed(2);
    }

    manoObra.addEventListener('input', calcularTotal);
    repuestos.addEventListener('input', calcularTotal);
</script>
@endsection
