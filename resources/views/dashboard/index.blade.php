@extends('layouts.dashboard')

@section('title', 'Panel Principal')

@section('content')
<div class="container-fluid">
    {{-- ENCABEZADO --}}
    <div class="mb-4" data-aos="fade-down">
        <h2 class="fw-bold text-dark">Panel de Control</h2>
        <p class="text-muted">Bienvenido al sistema de gestión integral</p>
    </div>

    {{-- TARJETAS DE ESTADÍSTICAS --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="stat-card">
                <div class="icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <p>Ventas del Mes</p>
                <h3>S/ {{ number_format($stats['ventas_mes'], 2) }}</h3>
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> +12.5% vs mes anterior
                </small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="stat-card">
                <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-users"></i>
                </div>
                <p>Total Clientes</p>
                <h3>{{ number_format($stats['total_clientes']) }}</h3>
                <small class="text-success">
                    <i class="fas fa-arrow-up"></i> +{{ $stats['nuevos_clientes'] }} nuevos
                </small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="stat-card">
                <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-box"></i>
                </div>
                <p>Productos Activos</p>
                <h3>{{ number_format($stats['productos_activos']) }}</h3>
                <small class="text-warning">
                    <i class="fas fa-exclamation-circle"></i> {{ $stats['stock_bajo'] }} con stock bajo
                </small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="stat-card">
                <div class="icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <p>Cuotas Pendientes</p>
                <h3>S/ {{ number_format($stats['cuotas_pendientes'], 2) }}</h3>
                <small class="text-danger">
                    <i class="fas fa-clock"></i> {{ $stats['cuotas_vencidas'] }} vencidas
                </small>
            </div>
        </div>
    </div>

    {{-- GRÁFICOS --}}
    <div class="row g-4 mb-4">
        {{-- Ventas Mensuales --}}
        <div class="col-xl-8" data-aos="fade-up">
            <div class="chart-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5><i class="fas fa-chart-line text-primary"></i> Ventas Mensuales</h5>
                    <select class="form-select form-select-sm" style="width: auto;" id="yearSelector">
                        <option value="2025" selected>2025</option>
                        <option value="2024">2024</option>
                    </select>
                </div>
                <canvas id="ventasChart" height="80"></canvas>
            </div>
        </div>

        {{-- Productos Más Vendidos --}}
        <div class="col-xl-4" data-aos="fade-up" data-aos-delay="100">
            <div class="chart-card">
                <h5><i class="fas fa-trophy text-warning"></i> Top Productos</h5>
                <canvas id="productosChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Stock Crítico --}}
        <div class="col-xl-6" data-aos="fade-up">
            <div class="chart-card">
                <h5><i class="fas fa-exclamation-triangle text-danger"></i> Stock Bajo</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Stock Actual</th>
                                <th>Stock Mínimo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productosStockBajo as $producto)
                            <tr>
                                <td>
                                    @if($producto->imagen)
                                        <img src="{{ asset('storage/' . $producto->imagen) }}" width="40" height="40" class="rounded me-2" onerror="this.src='https://via.placeholder.com/40'">
                                    @else
                                        <div class="d-inline-block bg-secondary rounded me-2" style="width: 40px; height: 40px;"></div>
                                    @endif
                                    <strong>{{ $producto->nombre }}</strong>
                                </td>
                                <td><span class="badge bg-danger">{{ $producto->stock }}</span></td>
                                <td>{{ $producto->stock_minimo }}</td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-circle"></i> Crítico
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Cuotas por Vencer --}}
        <div class="col-xl-6" data-aos="fade-up" data-aos-delay="100">
            <div class="chart-card">
                <h5><i class="fas fa-calendar-alt text-info"></i> Cuotas por Vencer</h5>
                <canvas id="cuotasChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Datos de ventas mensuales (desde el backend)
    const ventasData = @json($ventasMensuales);
    
    // Gráfico de Ventas Mensuales
    const ventasCtx = document.getElementById('ventasChart').getContext('2d');
    new Chart(ventasCtx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ventas (S/)',
                data: ventasData,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Gráfico de Productos Más Vendidos
    const productosData = @json($topProductos);
    const productosCtx = document.getElementById('productosChart').getContext('2d');
    new Chart(productosCtx, {
        type: 'doughnut',
        data: {
            labels: productosData.map(p => p.nombre),
            datasets: [{
                data: productosData.map(p => p.cantidad),
                backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Gráfico de Cuotas
    const cuotasData = @json($cuotasChart);
    const cuotasCtx = document.getElementById('cuotasChart').getContext('2d');
    new Chart(cuotasCtx, {
        type: 'bar',
        data: {
            labels: ['Pagadas', 'Pendientes', 'Vencidas'],
            datasets: [{
                label: 'Cantidad',
                data: cuotasData,
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
@endsection