@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8f9fa;">
    {{-- Header Principal --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1 fw-bold text-dark">Dashboard Fiscal</h2>
                    <p class="text-secondary mb-0 fs-6">
                        {{ $mesReporte }} · Año Fiscal {{ date('Y') }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Imprimir
                    </button>
                    <button class="btn btn-dark btn-sm" onclick="exportarDatos()">
                        <i class="fas fa-download me-1"></i>Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards Principales de Resumen --}}
    <div class="row g-3 mb-4">
        {{-- Utilidad Bruta --}}
        <div class="col-xl-4 col-md-6">
            <div class="card-minimal h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="icon-minimal mb-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <p class="text-muted mb-1 fs-7">Utilidad Bruta</p>
                        <h3 class="mb-0 fw-bold text-dark">S/. {{ number_format($utilidadBruta, 2) }}</h3>
                    </div>
                </div>
                <div class="progress-minimal">
                    <div class="progress-fill" style="width: 75%;"></div>
                </div>
            </div>
        </div>

        {{-- Total Ingresos --}}
        <div class="col-xl-4 col-md-6">
            <div class="card-minimal h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="icon-minimal mb-3 bg-success-subtle">
                            <i class="fas fa-arrow-up text-success"></i>
                        </div>
                        <p class="text-muted mb-1 fs-7">Total Ingresos</p>
                        <h3 class="mb-0 fw-bold text-dark">S/. {{ number_format($ingresosTotal, 2) }}</h3>
                    </div>
                </div>
                <small class="text-success">
                    <i class="fas fa-caret-up"></i> +12.5% vs mes anterior
                </small>
            </div>
        </div>

        {{-- Total Egresos --}}
        <div class="col-xl-4 col-md-6">
            <div class="card-minimal h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="icon-minimal mb-3 bg-danger-subtle">
                            <i class="fas fa-arrow-down text-danger"></i>
                        </div>
                        <p class="text-muted mb-1 fs-7">Total Egresos</p>
                        <h3 class="mb-0 fw-bold text-dark">S/. {{ number_format($egresosTotal, 2) }}</h3>
                    </div>
                </div>
                <small class="text-danger">
                    <i class="fas fa-caret-down"></i> -3.2% vs mes anterior
                </small>
            </div>
        </div>
    </div>

    {{-- Sección IGV --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-minimal">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="mb-0 fw-semibold text-dark">Impuesto General a las Ventas (IGV)</h5>
                    <span class="badge bg-dark">SUNAT</span>
                </div>

                <div class="row g-3">
                    {{-- IGV Débito --}}
                    <div class="col-lg-4">
                        <div class="igv-card">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <p class="text-muted mb-1 fs-7">IGV Débito Fiscal</p>
                                    <p class="mb-0 text-secondary" style="font-size: 0.75rem;">Ventas del mes</p>
                                </div>
                                <div class="icon-badge text-danger">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 fw-bold text-dark">S/. {{ number_format($igvVentas, 2) }}</h3>
                        </div>
                    </div>

                    {{-- IGV Crédito --}}
                    <div class="col-lg-4">
                        <div class="igv-card">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <p class="text-muted mb-1 fs-7">IGV Crédito Fiscal</p>
                                    <p class="mb-0 text-secondary" style="font-size: 0.75rem;">Compras y gastos</p>
                                </div>
                                <div class="icon-badge text-success">
                                    <i class="fas fa-minus"></i>
                                </div>
                            </div>
                            <h3 class="mb-0 fw-bold text-dark">S/. {{ number_format($igvEgresos, 2) }}</h3>
                        </div>
                    </div>

                    {{-- IGV Neto --}}
                    <div class="col-lg-4">
                        <div class="igv-card @if($igvACargo > 0) border-start border-danger border-3 @else border-start border-success border-3 @endif">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <p class="text-muted mb-1 fs-7">
                                        @if($igvACargo > 0) IGV a Pagar @else Saldo a Favor @endif
                                    </p>
                                    <p class="mb-0 text-secondary" style="font-size: 0.75rem;">Resultado neto</p>
                                </div>
                                <div class="icon-badge @if($igvACargo > 0) text-danger @else text-success @endif">
                                    <i class="fas @if($igvACargo > 0) fa-exclamation @else fa-check @endif"></i>
                                </div>
                            </div>
                            <h3 class="mb-2 fw-bold text-dark">S/. {{ number_format(abs($igvACargo), 2) }}</h3>
                            @if($igvACargo > 0)
                                <span class="badge-minimal bg-danger">Vence: 15/{{ date('m/Y') }}</span>
                            @else
                                <span class="badge-minimal bg-success">Crédito disponible</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Proyección Renta + Checklist --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card-minimal h-100">
                <h5 class="mb-4 fw-semibold text-dark border-bottom pb-3">Proyección Impuesto a la Renta</h5>
                
                <div class="text-center py-3">
                    <div class="d-flex justify-content-center mb-4">
                        <div style="width: 190px; height: 190px;">
                            <canvas id="rentaChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="renta-info">
                        <p class="text-muted mb-2">Estimación anual · Tasa 30%</p>
                        <h2 class="mb-3 fw-bold text-dark">S/. {{ number_format($rentaAnualPagar, 2) }}</h2>
                        <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                            Proyección total año {{ date('Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-minimal h-100">
                <h5 class="mb-4 fw-semibold text-dark border-bottom pb-3">Checklist Mensual</h5>
                
                <div class="checklist">
                    <div class="checklist-item">
                        <input type="checkbox" class="form-check-input" checked>
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Declaración PDT 621</p>
                            <small class="text-muted">IGV - Renta Mensual</small>
                        </div>
                        <span class="badge-minimal bg-success">Completado</span>
                    </div>
                    
                    <div class="checklist-item">
                        <input type="checkbox" class="form-check-input">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Libros Electrónicos PLE</p>
                            <small class="text-muted">Registro de Ventas y Compras</small>
                        </div>
                        <span class="badge-minimal bg-warning">Pendiente</span>
                    </div>
                    
                    <div class="checklist-item">
                        <input type="checkbox" class="form-check-input">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">PLAME - Planilla Electrónica</p>
                            <small class="text-muted">Si aplica</small>
                        </div>
                        <span class="badge-minimal bg-secondary">N/A</span>
                    </div>
                    
                    <div class="checklist-item">
                        <input type="checkbox" class="form-check-input">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-medium">Conciliación Bancaria</p>
                            <small class="text-muted">Verificar movimientos</small>
                        </div>
                        <span class="badge-minimal bg-primary">En proceso</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico de Tendencia + Resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card-minimal">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-semibold text-dark">Tendencia Anual</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-secondary active">Todo</button>
                        <button type="button" class="btn btn-outline-secondary">Trimestre</button>
                        <button type="button" class="btn btn-outline-secondary">Semestre</button>
                    </div>
                </div>
                <div style="position: relative; height: 300px;">
                    <canvas id="chartBalanceAnual"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-minimal h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0 fw-semibold text-dark">Resumen de Obligaciones</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="imprimirResumen()">
                        <i class="fas fa-print"></i>
                    </button>
                </div>

                {{-- IGV --}}
                <div class="obligation-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-medium">IGV del Mes</span>
                        <span class="badge-minimal bg-danger">Urgente</span>
                    </div>
                    <h4 class="mb-2 fw-bold text-dark">S/. {{ number_format(abs($igvACargo), 2) }}</h4>
                    <p class="text-muted mb-3" style="font-size: 0.85rem;">
                        <i class="far fa-calendar me-1"></i>Vence: 15/{{ date('m/Y') }}
                    </p>
                    <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                        <span class="text-muted">Débito:</span>
                        <span>S/. {{ number_format($igvVentas, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                        <span class="text-muted">Crédito:</span>
                        <span class="text-success">-S/. {{ number_format($igvEgresos, 2) }}</span>
                    </div>
                </div>

                {{-- Renta --}}
                <div class="obligation-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-medium">Renta Anual</span>
                        <span class="badge-minimal bg-secondary">Proyección</span>
                    </div>
                    <h4 class="mb-2 fw-bold text-dark">S/. {{ number_format($rentaAnualPagar, 2) }}</h4>
                    <p class="text-muted mb-3" style="font-size: 0.85rem;">
                        <i class="fas fa-calculator me-1"></i>30% sobre ventas brutas
                    </p>
                    <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                        <span class="text-muted">Base Imponible:</span>
                        <span>S/. {{ number_format($rentaAnualPagar / 0.30, 2) }}</span>
                    </div>
                </div>

                {{-- Total --}}
                <div class="total-obligation">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-1 text-white opacity-75" style="font-size: 0.85rem;">Total Mensual Estimado</p>
                            <h4 class="mb-0 fw-bold text-white">S/. {{ number_format(abs($igvACargo) + ($rentaAnualPagar / 12), 2) }}</h4>
                        </div>
                        <i class="fas fa-coins fa-2x text-white opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Justificación Legal --}}
    <div class="row">
        <div class="col-12">
            <div class="card-minimal">
                <h5 class="mb-4 fw-semibold text-dark border-bottom pb-3">Base Legal y Justificación Contable</h5>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="legal-section">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-gavel me-2 text-primary"></i>
                                IGV - Impuesto General a las Ventas
                            </h6>
                            <p class="text-muted mb-3" style="font-size: 0.9rem;">
                                <strong>Base Legal:</strong> D.Leg. N° 821 y TUO del IGV (D.S. N° 055-99-EF)
                            </p>
                            <ul class="legal-list">
                                <li>Débito Fiscal: IGV cobrado en ventas del mes</li>
                                <li>Crédito Fiscal: IGV pagado en compras válidas</li>
                                <li>IGV Neto: Diferencia entre débito y crédito</li>
                                <li>Vencimiento: Día 15 del mes siguiente según RUC</li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="legal-section">
                            <h6 class="fw-semibold mb-3">
                                <i class="fas fa-landmark me-2 text-success"></i>
                                Impuesto a la Renta Anual
                            </h6>
                            <p class="text-muted mb-3" style="font-size: 0.9rem;">
                                <strong>Base Legal:</strong> TUO Ley del IR (D.S. N° 179-2004-EF)
                            </p>
                            <ul class="legal-list">
                                <li>Régimen: Régimen General (30% sobre renta neta)</li>
                                <li>Cálculo: (Ingresos - Gastos Deducibles) x 30%</li>
                                <li>Proyección: Basada en ventas brutas acumuladas</li>
                                <li>Declaración: Anual hasta abril (PDT 708)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="alert-minimal mt-4">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-info-circle fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-semibold mb-2">Consideraciones Importantes:</h6>
                            <ul class="mb-0" style="font-size: 0.9rem;">
                                <li>Conservar respaldos de todas las facturas (ventas y compras)</li>
                                <li>Verificar requisitos SUNAT para crédito fiscal válido</li>
                                <li>La proyección anual es referencial; el cálculo final considera gastos deducibles</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        --card-shadow-hover: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    }

    .card-minimal {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.75rem;
        box-shadow: var(--card-shadow);
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .card-minimal:hover {
        box-shadow: var(--card-shadow-hover);
    }

    .icon-minimal {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background-color: #f3f4f6;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-size: 1.25rem;
    }

    .icon-badge {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background-color: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
    }

    .progress-minimal {
        height: 4px;
        background-color: #f3f4f6;
        border-radius: 2px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background-color: #374151;
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    .igv-card {
        background: #fafafa;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        height: 100%;
        transition: all 0.2s ease;
    }

    .igv-card:hover {
        background: #ffffff;
        transform: translateY(-2px);
    }

    .badge-minimal {
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        color: white;
    }

    .checklist {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .checklist-item {
        display: flex;
        align-items: start;
        gap: 0.75rem;
        padding: 1rem;
        background: #fafafa;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .checklist-item:hover {
        background: #f3f4f6;
    }

    .checklist-item .form-check-input {
        margin-top: 0.25rem;
    }

    .obligation-card {
        padding: 1.25rem;
        background: #fafafa;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
    }

    .total-obligation {
        padding: 1.5rem;
        background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
        border-radius: 10px;
        margin-top: 1rem;
    }

    .legal-section {
        padding: 1.25rem;
        background: #fafafa;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        height: 100%;
    }

    .legal-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .legal-list li {
        padding: 0.5rem 0;
        padding-left: 1.5rem;
        position: relative;
        font-size: 0.9rem;
        color: #4b5563;
    }

    .legal-list li:before {
        content: "·";
        position: absolute;
        left: 0.5rem;
        color: #9ca3af;
        font-weight: bold;
    }

    .alert-minimal {
        background: #f0f9ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 1.25rem;
    }

    .fs-7 {
        font-size: 0.875rem;
    }

    @media print {
        .btn, .topbar, #sidebar {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($labelsAnuales ?? []);
        const ingresos = @json($dataIngresos ?? []);
        const egresos = @json($dataEgresos ?? []);
        const utilidad = @json($dataUtilidad ?? []);
        
        // Configuración común de colores minimalistas
        const colors = {
            primary: '#374151',
            success: '#10b981',
            danger: '#ef4444',
            grid: '#e5e7eb'
        };

        // Gráfico de líneas - Balance Anual
        const ctx = document.getElementById('chartBalanceAnual').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ingresos',
                        data: ingresos,
                        borderColor: colors.success,
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.success,
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Egresos',
                        data: egresos,
                        borderColor: colors.danger,
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.danger,
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Utilidad',
                        data: utilidad,
                        borderColor: colors.primary,
                        backgroundColor: 'rgba(55, 65, 81, 0.05)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: colors.primary,
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            color: '#6b7280'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': S/. ' + context.parsed.y.toLocaleString('es-PE', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: colors.grid },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af',
                            callback: function(value) {
                                return 'S/. ' + value.toLocaleString('es-PE');
                            }
                        }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Gráfico circular - Renta Anual
        const ctxRenta = document.getElementById('rentaChart').getContext('2d');
        new Chart(ctxRenta, {
            type: 'doughnut',
            data: {
                labels: ['Impuesto (30%)', 'Utilidad Neta (70%)'],
                datasets: [{
                    data: [30, 70],
                    backgroundColor: ['#ef4444', '#10b981'],
                    borderWidth: 0,
                    spacing: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '70%',
                plugins: {
                    legend: { 
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: { size: 11 },
                            color: '#6b7280'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    });

    function exportarDatos() {
        Swal.fire({
            title: 'Exportar Datos',
            text: 'Selecciona el formato de exportación',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-file-excel me-2"></i>Excel',
            cancelButtonText: '<i class="fas fa-file-pdf me-2"></i>PDF',
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#374151',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-dark'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Exportando...',
                    text: 'Generando archivo Excel',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    icon: 'success',
                    title: 'Exportando...',
                    text: 'Generando archivo PDF',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function imprimirResumen() {
        const contenido = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Resumen Tributario - {{ $mesReporte }}</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { 
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                        padding: 40px;
                        color: #374151;
                        line-height: 1.6;
                    }
                    .header { 
                        text-align: center;
                        margin-bottom: 40px;
                        padding-bottom: 20px;
                        border-bottom: 2px solid #e5e7eb;
                    }
                    .header h1 {
                        font-size: 24px;
                        font-weight: 700;
                        color: #1f2937;
                        margin-bottom: 10px;
                    }
                    .header p {
                        color: #6b7280;
                        font-size: 14px;
                    }
                    .section {
                        margin: 30px 0;
                        padding: 20px;
                        border: 1px solid #e5e7eb;
                        border-radius: 8px;
                        background: #fafafa;
                    }
                    .section h2 {
                        font-size: 18px;
                        font-weight: 600;
                        margin-bottom: 15px;
                        color: #1f2937;
                    }
                    .destacado {
                        background: #ffffff;
                        padding: 25px;
                        margin: 20px 0;
                        border-left: 4px solid #ef4444;
                        border-radius: 8px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 15px 0;
                    }
                    td {
                        padding: 12px 8px;
                        border-bottom: 1px solid #f3f4f6;
                        font-size: 14px;
                    }
                    .label {
                        font-weight: 500;
                        color: #6b7280;
                    }
                    .monto {
                        text-align: right;
                        font-size: 16px;
                        font-weight: 600;
                        color: #1f2937;
                    }
                    .total-row {
                        border-top: 2px solid #1f2937;
                        background: #f9fafb;
                    }
                    .total-row td {
                        padding: 15px 8px;
                        font-weight: 700;
                        font-size: 18px;
                        color: #ef4444;
                    }
                    .footer {
                        margin-top: 40px;
                        padding-top: 20px;
                        border-top: 1px solid #e5e7eb;
                        font-size: 12px;
                        color: #9ca3af;
                    }
                    .badge {
                        display: inline-block;
                        padding: 4px 12px;
                        border-radius: 4px;
                        font-size: 12px;
                        font-weight: 500;
                        background: #fee2e2;
                        color: #dc2626;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>RESUMEN DE OBLIGACIONES TRIBUTARIAS</h1>
                    <p><strong>Período:</strong> {{ $mesReporte }} | <strong>Fecha:</strong> ${new Date().toLocaleDateString('es-PE', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                </div>

                <div class="destacado">
                    <h2>OBLIGACIÓN MENSUAL - IGV</h2>
                    <table>
                        <tr>
                            <td class="label">IGV Débito Fiscal (Ventas)</td>
                            <td class="monto">S/. {{ number_format($igvVentas, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label">IGV Crédito Fiscal (Compras)</td>
                            <td class="monto" style="color: #10b981;">-S/. {{ number_format($igvEgresos, 2) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td class="label">TOTAL IGV A PAGAR</td>
                            <td class="monto">S/. {{ number_format(abs($igvACargo), 2) }}</td>
                        </tr>
                    </table>
                    <p style="margin-top: 15px;">
                        <span class="badge">VENCIMIENTO: 15/{{ date('m/Y') }}</span>
                    </p>
                </div>

                <div class="section">
                    <h2>PROYECCIÓN RENTA ANUAL {{ date('Y') }}</h2>
                    <table>
                        <tr>
                            <td class="label">Base Imponible (Ventas Brutas)</td>
                            <td class="monto">S/. {{ number_format($rentaAnualPagar / 0.30, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tasa del Impuesto a la Renta</td>
                            <td class="monto">30%</td>
                        </tr>
                        <tr class="total-row">
                            <td class="label">ESTIMADO RENTA ANUAL</td>
                            <td class="monto">S/. {{ number_format($rentaAnualPagar, 2) }}</td>
                        </tr>
                    </table>
                </div>

                <div class="section">
                    <h2>JUSTIFICACIÓN LEGAL</h2>
                    <p style="margin-bottom: 10px;">
                        <strong>IGV:</strong> Calculado según D.Leg. N° 821 y TUO del IGV (D.S. N° 055-99-EF)
                    </p>
                    <p>
                        <strong>Impuesto a la Renta:</strong> Según TUO Ley del IR (D.S. N° 179-2004-EF) - Régimen General 30%
                    </p>
                </div>

                <div class="footer">
                    <p><strong>Nota:</strong> Este documento es un resumen contable generado automáticamente.</p>
                    <p>Conserve los comprobantes originales para cualquier fiscalización de SUNAT.</p>
                </div>
            </body>
            </html>
        `;
        
        const ventana = window.open('', '_blank');
        ventana.document.write(contenido);
        ventana.document.close();
        ventana.focus();
        setTimeout(() => {
            ventana.print();
            ventana.close();
        }, 250);
    }
</script>
@endpush
@endsection