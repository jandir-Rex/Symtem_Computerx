@extends('layouts.app')

@section('title', 'Dashboard - Stand 1')

@section('content')
<div class="container py-4">
    <h1 class="text-center mb-4 fw-bold">📊 Dashboard General - Stand 1</h1>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5>Total Ventas</h5>
                    <h3 class="fw-bold text-success">S/ {{ number_format($ventasTotales, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5>Ingresos del Día</h5>
                    <h3 class="fw-bold text-primary">S/ {{ number_format($ingresosDelDia, 2) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5>Ventas Pendientes</h5>
                    <h3 class="fw-bold text-warning">{{ $ventasPendientes }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('stands.stand1.ventas.index') }}" class="btn btn-outline-success me-3">
            <i class="fas fa-cash-register"></i> Ir a Ventas
        </a>
    </div>
</div>
@endsection
