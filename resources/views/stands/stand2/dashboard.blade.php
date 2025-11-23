@extends('layouts.app')

@section('title', 'Dashboard - Stand 2')

@section('content')
<div class="container py-4">
    <h1 class="text-center mb-4 fw-bold">📊 Dashboard General - Stand 2</h1>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5>Total Ventas</h5>
                    <h3 class="fw-bold text-success">S/ {{ number_format($ventasTotales, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5>Ingresos del Día</h5>
                    <h3 class="fw-bold text-primary">S/ {{ number_format($ingresosDelDia, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5>Ventas Pendientes</h5>
                    <h3 class="fw-bold text-warning">{{ $ventasPendientes }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5>Reparaciones Activas</h5>
                    <h3 class="fw-bold text-danger">{{ $reparacionesActivas }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if($reparacionesPorEstado->count())
    <div class="row mt-4 g-4">
        @foreach($reparacionesPorEstado as $estado => $total)
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-uppercase">{{ $estado }}</h6>
                    <h4>{{ $total }}</h4>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="text-center mt-5">
        <a href="{{ route('stands.stand2.ventas.index') }}" class="btn btn-outline-success me-3">Ir a Ventas</a>
        <a href="{{ route('stands.stand2.reparaciones.index') }}" class="btn btn-outline-danger">Ir a Reparaciones</a>
    </div>
</div>
@endsection
