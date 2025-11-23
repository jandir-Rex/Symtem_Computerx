@extends('layouts.app')

@section('title', 'Detalle Reparación')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary mb-0">Reparación #{{ $reparacion->id }}</h2>
        
        {{-- ✅ BOTÓN DESCARGAR COMPROBANTE EN LA PARTE SUPERIOR --}}
        <a href="{{ route('stands.stand2.reparaciones.descargar-comprobante', $reparacion->id) }}" 
           class="btn btn-success" 
           target="_blank">
            <i class="fas fa-download"></i> Descargar Comprobante PDF
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Información del cliente y equipo
        </div>
        <div class="card-body">
            <p><strong>Cliente:</strong> {{ $reparacion->cliente_nombre }} ({{ $reparacion->cliente_telefono }})</p>
            <p><strong>Equipo:</strong> {{ $reparacion->tipo_equipo }} {{ $reparacion->marca }} {{ $reparacion->modelo }}</p>
            <p><strong>Número de serie:</strong> {{ $reparacion->numero_serie }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Diagnóstico y solución
        </div>
        <div class="card-body">
            <p><strong>Problema reportado:</strong> {{ $reparacion->problema_reportado }}</p>
            <p><strong>Diagnóstico:</strong> {{ $reparacion->diagnostico }}</p>
            <p><strong>Solución aplicada:</strong> {{ $reparacion->solucion_aplicada }}</p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header bg-dark text-white">
            Costos y fechas
        </div>
        <div class="card-body">
            <p><strong>Mano de obra:</strong> {{ number_format($reparacion->costo_mano_obra,2) }}</p>
            <p><strong>Repuestos:</strong> {{ number_format($reparacion->costo_repuestos,2) }}</p>
            <p><strong>Total:</strong> {{ number_format($reparacion->costo_total,2) }}</p>
            <p><strong>Fecha ingreso:</strong> {{ $reparacion->fecha_ingreso?->format('d/m/Y') }}</p>
            <p><strong>Fecha estimada entrega:</strong> {{ $reparacion->fecha_estimada_entrega?->format('d/m/Y') }}</p>
            <p><strong>Fecha entrega real:</strong> {{ $reparacion->fecha_entrega_real?->format('d/m/Y') }}</p>
            <p><strong>Estado:</strong> {{ $reparacion->estado }}</p>
            <p><strong>Prioridad:</strong> {{ $reparacion->prioridad }}</p>
            <p><strong>Calificación:</strong> {{ $reparacion->calificacion }}</p>
            <p><strong>Notas internas:</strong> {{ $reparacion->notas_internas }}</p>
        </div>
    </div>

    <a href="{{ route('stands.stand2.reparaciones.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection