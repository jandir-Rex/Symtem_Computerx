<div class="row g-3 mb-4">
    {{-- Ventas del Día --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-cart-check text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">Ventas del Día</h6>
                        <h4 class="mb-0 fw-bold text-primary">{{ $estadisticas['ventas_dia'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Reparaciones Activas --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-tools text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">Reparaciones Activas</h6>
                        <h4 class="mb-0 fw-bold text-warning">{{ $estadisticas['reparaciones_activas'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Créditos Pendientes --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-credit-card text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">Créditos Pendientes</h6>
                        <h4 class="mb-0 fw-bold text-info">{{ $estadisticas['creditos_pendientes'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Recaudado --}}
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-cash-stack text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1 small">Total Recaudado</h6>
                        <h4 class="mb-0 fw-bold text-success">S/ {{ number_format($estadisticas['total_recaudado'] ?? 0, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
