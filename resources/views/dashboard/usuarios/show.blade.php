@extends('layouts.dashboard')

@section('title', 'Detalle de Usuario')

@section('content')
<div class="container-fluid px-4">
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="fas {{ $esClienteEcommerce ? 'fa-shopping-bag' : 'fa-user-tie' }} text-primary"></i> 
                Detalle de {{ $esClienteEcommerce ? 'Cliente' : 'Usuario' }}
            </h4>
        </div>
        <div>
            <a href="{{ route('dashboard.usuarios.index', ['tipo' => $esClienteEcommerce ? 'clientes' : 'sistema']) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            @if(!$esClienteEcommerce)
            <a href="{{ route('dashboard.usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endif
        </div>
    </div>

    <div class="row g-3">
        {{-- INFORMACIÓN PERSONAL --}}
        <div class="col-lg-4 col-md-5">
            <div class="card mb-3 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="user-avatar mx-auto mb-2" style="width: 70px; height: 70px; background: {{ $esClienteEcommerce ? '#10b981' : '#4f46e5' }}; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.8rem;">
                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                    </div>
                    <h5 class="mb-2">{{ $usuario->name }}</h5>
                    @if(!$esClienteEcommerce)
                        <span class="badge bg-primary mb-2">
                            {{ $usuario->roles->first()->name ?? 'Sin rol' }}
                        </span>
                    @else
                        <span class="badge bg-success mb-2">
                            Cliente E-commerce
                        </span>
                    @endif
                    
                    <div class="mb-2">
                        <div class="form-check form-switch d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" 
                                   {{ $usuario->active ? 'checked' : '' }} disabled>
                            <label class="form-check-label ms-2 small">
                                {{ $usuario->active ? 'Activo' : 'Inactivo' }}
                            </label>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="text-start">
                        <p class="mb-1 small">
                            <i class="fas fa-envelope text-muted me-2"></i>
                            {{ $usuario->email }}
                        </p>
                        @if($usuario->dni)
                        <p class="mb-1 small">
                            <i class="fas fa-id-card text-muted me-2"></i>
                            {{ $usuario->dni }}
                        </p>
                        @endif
                        @if($usuario->telefono)
                        <p class="mb-1 small">
                            <i class="fas fa-phone text-muted me-2"></i>
                            {{ $usuario->telefono }}
                        </p>
                        @endif
                        @if($usuario->direccion)
                        <p class="mb-1 small">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            {{ $usuario->direccion }}
                        </p>
                        @endif
                        @if(!$esClienteEcommerce && $usuario->stand_id)
                        <p class="mb-1 small">
                            <i class="fas fa-store text-muted me-2"></i>
                            Stand {{ $usuario->stand_id }}
                        </p>
                        @endif
                        <p class="mb-0 small">
                            <i class="fas fa-calendar text-muted me-2"></i>
                            Registrado: {{ $usuario->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ESTADÍSTICAS Y ACTIVIDAD --}}
        <div class="col-lg-8 col-md-7">
            @if($stats)
            <div class="card mb-3 shadow-sm">
                <div class="card-header py-2 bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-chart-line text-primary"></i> Estadísticas
                    </h6>
                </div>
                <div class="card-body py-3">
                    <div class="row g-2">
                        @if($esClienteEcommerce)
                            {{-- Estadísticas de Cliente E-commerce --}}
                            <div class="col-xl-3 col-6">
                                <div class="text-center p-2 rounded" style="background: rgba(59, 130, 246, 0.05);">
                                    <div class="mb-1" style="color: #3b82f6;">
                                        <i class="fas fa-shopping-cart fa-lg"></i>
                                    </div>
                                    <small class="text-muted d-block">Total Compras</small>
                                    <h5 class="mb-0">{{ $stats['total_compras'] }}</h5>
                                </div>
                            </div>
                            <div class="col-xl-3 col-6">
                                <div class="text-center p-2 rounded" style="background: rgba(16, 185, 129, 0.05);">
                                    <div class="mb-1" style="color: #10b981;">
                                        <i class="fas fa-dollar-sign fa-lg"></i>
                                    </div>
                                    <small class="text-muted d-block">Total Gastado</small>
                                    <h5 class="mb-0">S/ {{ number_format($stats['monto_total'], 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-xl-3 col-6">
                                <div class="text-center p-2 rounded" style="background: rgba(245, 158, 11, 0.05);">
                                    <div class="mb-1" style="color: #f59e0b;">
                                        <i class="fas fa-clock fa-lg"></i>
                                    </div>
                                    <small class="text-muted d-block">Pendientes</small>
                                    <h5 class="mb-0">{{ $stats['compras_pendientes'] }}</h5>
                                </div>
                            </div>
                            <div class="col-xl-3 col-6">
                                <div class="text-center p-2 rounded" style="background: rgba(239, 68, 68, 0.05);">
                                    <div class="mb-1" style="color: #ef4444;">
                                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                                    </div>
                                    <small class="text-muted d-block">Deuda</small>
                                    <h5 class="mb-0">S/ {{ number_format($stats['total_deuda'] ?? 0, 2) }}</h5>
                                </div>
                            </div>

                            @if($stats['ultima_compra'])
                            <div class="col-12 mt-2">
                                <div class="alert alert-info mb-0 py-2 small">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Última compra:</strong> 
                                    {{ $stats['ultima_compra']->created_at->format('d/m/Y H:i') }} - 
                                    S/ {{ number_format($stats['ultima_compra']->total, 2) }}
                                </div>
                            </div>
                            @endif
                        @else
                            {{-- Estadísticas de Vendedor --}}
                            <div class="col-md-4">
                                <div class="text-center p-2 rounded" style="background: rgba(59, 130, 246, 0.05);">
                                    <div class="mb-1" style="color: #3b82f6;">
                                        <i class="fas fa-receipt fa-lg"></i>
                                    </div>
                                    <small class="text-muted d-block">Ventas Totales</small>
                                    <h5 class="mb-0">{{ $stats['ventas_totales'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-2 rounded" style="background: rgba(16, 185, 129, 0.05);">
                                    <div class="mb-1" style="color: #10b981;">
                                        <i class="fas fa-dollar-sign fa-lg"></i>
                                    </div>
                                    <small class="text-muted d-block">Monto Vendido</small>
                                    <h5 class="mb-0">S/ {{ number_format($stats['monto_vendido'], 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-2 rounded" style="background: rgba(245, 158, 11, 0.05);">
                                    <div class="mb-1" style="color: #f59e0b;">
                                        <i class="fas fa-chart-line fa-lg"></i>
                                    </div>
                                    <small class="text-muted d-block">Promedio Venta</small>
                                    <h5 class="mb-0">S/ {{ $stats['ventas_totales'] > 0 ? number_format($stats['monto_vendido'] / $stats['ventas_totales'], 2) : '0.00' }}</h5>
                                </div>
                            </div>

                            @if($stats['ultima_venta'])
                            <div class="col-12 mt-2">
                                <div class="alert alert-info mb-0 py-2 small">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Última venta:</strong> 
                                    {{ $stats['ultima_venta']->created_at->format('d/m/Y H:i') }} - 
                                    S/ {{ number_format($stats['ultima_venta']->total, 2) }}
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- ACTIVIDAD RECIENTE --}}
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-history text-primary"></i> Actividad Reciente
                    </h6>
                </div>
                <div class="card-body py-3">
                    @if($esClienteEcommerce && isset($stats['ultima_compra']))
                        <p class="text-muted mb-2 small">Historial de compras del cliente</p>
                        <div class="alert alert-secondary mb-0 py-2 small">
                            <i class="fas fa-info-circle"></i> 
                            Este cliente tiene {{ $stats['total_compras'] }} compra(s) registrada(s).
                        </div>
                    @elseif(!$esClienteEcommerce && isset($stats['ventas_totales']) && $stats['ventas_totales'] > 0)
                        <p class="text-muted mb-2 small">Ventas registradas por este usuario</p>
                        <div class="alert alert-secondary mb-0 py-2 small">
                            <i class="fas fa-info-circle"></i> 
                            Este usuario ha registrado {{ $stats['ventas_totales'] }} venta(s).
                        </div>
                    @else
                        <div class="alert alert-warning mb-0 py-2 small">
                            <i class="fas fa-exclamation-circle"></i> 
                            No hay actividad registrada aún.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ACCIONES RÁPIDAS --}}
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-cogs text-primary"></i> Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body py-2">
                    <div class="d-flex gap-2 flex-wrap">
                        @if(!$esClienteEcommerce)
                        <a href="{{ route('dashboard.usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Editar Usuario
                        </a>
                        @endif
                        
                        @if($usuario->id != auth()->id())
                        <form action="{{ route('dashboard.usuarios.destroy', $usuario->id) }}" 
                              method="POST" class="d-inline"
                              onsubmit="return confirm('¿Estás seguro de eliminar este {{ $esClienteEcommerce ? 'cliente' : 'usuario' }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </form>
                        @endif
                        
                        <a href="{{ route('dashboard.usuarios.index', ['tipo' => $esClienteEcommerce ? 'clientes' : 'sistema']) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver al Listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection