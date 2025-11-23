@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('stand2.dashboard') }}" class="text-decoration-none">Stand 2</a></li>
            <li class="breadcrumb-item active" aria-current="page">Créditos</li>
        </ol>
    </nav>

    <!-- Título y botón de acción -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold text-success"><i class="bi bi-credit-card-2-front me-2"></i>Gestión de Créditos</h4>
        <a href="{{ route('stand2.creditos.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Crédito
        </a>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabla de créditos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($creditos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-semibold">#</th>
                                <th class="fw-semibold">Cliente</th>
                                <th class="fw-semibold">Venta ID</th>
                                <th class="fw-semibold">Monto Total</th>
                                <th class="fw-semibold">Pagado</th>
                                <th class="fw-semibold">Saldo Pendiente</th>
                                <th class="fw-semibold">Fecha Inicio</th>
                                <th class="fw-semibold">Vencimiento</th>
                                <th class="fw-semibold">Estado</th>
                                <th class="fw-semibold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditos as $credito)
                                <tr>
                                    <td class="text-muted">{{ $credito->id }}</td>
                                    <td>{{ $credito->cliente->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">#{{ $credito->venta_id }}</span>
                                    </td>
                                    <td>S/ {{ number_format($credito->monto_total, 2) }}</td>
                                    <td class="text-success">S/ {{ number_format($credito->monto_pagado, 2) }}</td>
                                    <td class="fw-semibold text-warning">S/ {{ number_format($credito->saldo_pendiente, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($credito->fecha_inicio)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($credito->fecha_vencimiento)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($credito->estado == 'pendiente')
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @elseif($credito->estado == 'pagado')
                                            <span class="badge bg-success">Pagado</span>
                                        @elseif($credito->estado == 'vencido')
                                            <span class="badge bg-danger">Vencido</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($credito->estado) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="tooltip" title="Ver detalle">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if($credito->estado != 'pagado')
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Registrar pago">
                                                    <i class="bi bi-cash-coin"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-3">
                    {{ $creditos->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No hay créditos registrados aún.</p>
                    <a href="{{ route('stand2.creditos.create') }}" class="btn btn-success mt-3">
                        <i class="bi bi-plus-circle me-1"></i>Registrar Primer Crédito
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection