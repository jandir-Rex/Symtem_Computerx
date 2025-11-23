<div class="row">
    {{-- Información del Cliente --}}
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-user"></i> Información del Cliente
                </h6>
                <p><strong>Nombre:</strong> {{ $reparacion->cliente_nombre }}</p>
                <p><strong>Teléfono:</strong> {{ $reparacion->cliente_telefono ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Información del Equipo --}}
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-laptop"></i> Información del Equipo
                </h6>
                <p><strong>Tipo:</strong> {{ $reparacion->tipo_equipo }}</p>
                <p><strong>Marca:</strong> {{ $reparacion->marca ?? 'N/A' }}</p>
                <p><strong>Modelo:</strong> {{ $reparacion->modelo ?? 'N/A' }}</p>
                <p><strong>N° Serie:</strong> {{ $reparacion->numero_serie ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Detalles de la Reparación --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h6 class="fw-bold text-primary mb-3">
            <i class="fas fa-tools"></i> Detalles de la Reparación
        </h6>
        
        <div class="mb-3">
            <strong>Problema Reportado:</strong>
            <p class="text-muted">{{ $reparacion->problema_reportado ?? 'No especificado' }}</p>
        </div>

        @if($reparacion->diagnostico)
            <div class="mb-3">
                <strong>Diagnóstico:</strong>
                <p class="text-muted">{{ $reparacion->diagnostico }}</p>
            </div>
        @endif

        @if($reparacion->solucion_aplicada)
            <div class="mb-3">
                <strong>Solución Aplicada:</strong>
                <p class="text-muted">{{ $reparacion->solucion_aplicada }}</p>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <p><strong>Estado:</strong> 
                    <span class="badge 
                        @if($reparacion->estado === 'recibido') bg-secondary
                        @elseif($reparacion->estado === 'en_reparacion') bg-warning
                        @elseif($reparacion->estado === 'listo') bg-info
                        @elseif($reparacion->estado === 'entregado') bg-success
                        @else bg-danger
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $reparacion->estado)) }}
                    </span>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong>Prioridad:</strong> 
                    <span class="badge 
                        @if($reparacion->prioridad === 'urgente') bg-danger
                        @elseif($reparacion->prioridad === 'alta') bg-warning
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($reparacion->prioridad ?? 'Normal') }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Costos --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h6 class="fw-bold text-primary mb-3">
            <i class="fas fa-dollar-sign"></i> Costos
        </h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <tbody>
                    <tr>
                        <td><strong>Mano de Obra:</strong></td>
                        <td class="text-end">S/ {{ number_format($reparacion->costo_mano_obra ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Repuestos:</strong></td>
                        <td class="text-end">S/ {{ number_format($reparacion->costo_repuestos ?? 0, 2) }}</td>
                    </tr>
                    <tr class="table-light">
                        <td><strong>TOTAL:</strong></td>
                        <td class="text-end fw-bold text-primary">S/ {{ number_format($reparacion->costo_total, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Fechas --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold text-primary mb-3">
            <i class="fas fa-calendar"></i> Fechas
        </h6>
        <div class="row">
            <div class="col-md-4">
                <p><strong>Ingreso:</strong><br>{{ $reparacion->created_at->format('d/m/Y H:i') }}</p>
            </div>
            @if($reparacion->fecha_estimada_entrega)
                <div class="col-md-4">
                    <p><strong>Entrega Estimada:</strong><br>{{ \Carbon\Carbon::parse($reparacion->fecha_estimada_entrega)->format('d/m/Y') }}</p>
                </div>
            @endif
            @if($reparacion->fecha_entrega_real)
                <div class="col-md-4">
                    <p><strong>Entrega Real:</strong><br>{{ \Carbon\Carbon::parse($reparacion->fecha_entrega_real)->format('d/m/Y') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>