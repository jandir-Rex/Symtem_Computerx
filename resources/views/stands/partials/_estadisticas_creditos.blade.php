<div class="row g-3 mb-4">
    @forelse($creditos as $credito)
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
                            <h6 class="text-muted mb-1 small">Cliente: {{ $credito->cliente->nombre ?? 'N/A' }}</h6>
                            <h6 class="text-muted mb-1 small">Venta ID: #{{ $credito->id }}</h6>
                            <h5 class="mb-0 fw-bold text-info">S/ {{ number_format($credito->total ?? 0, 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-secondary text-center">
                No hay créditos pendientes.
            </div>
        </div>
    @endforelse
</div>
