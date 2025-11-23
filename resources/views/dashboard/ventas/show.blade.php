@extends('layouts.dashboard')

@section('title', 'Detalle de Venta')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Detalle de la Venta #{{ $venta->id }}</h2>
            <p class="text-muted mb-0">
                <i class="fas fa-store"></i> Stand {{ $venta->stand_id }}
            </p>
        </div>

        {{-- ========================================================== --}}
        {{-- === BOTÓN DE DESCARGA PDF === --}}
        {{-- ========================================================== --}}
        <button 
            class="btn btn-success" 
            onclick="descargarComprobante()"
        >
            <i class="fas fa-download"></i> Descargar Comprobante PDF
        </button>
    </div>

    {{-- ========================================================== --}}
    {{-- === INFORMACIÓN GENERAL === --}}
    {{-- ========================================================== --}}
    <div class="card shadow-sm border-0 mb-4" data-aos="fade-up">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong><i class="fas fa-user text-primary"></i> Cliente:</strong> 
                        {{ $venta->cliente?->nombre ?? 'Sin cliente' }}
                    </p>
                    
                    @if($venta->cliente?->documento)
                        <p class="mb-2">
                            <strong><i class="fas fa-id-card text-primary"></i> Documento:</strong> 
                            {{ $venta->cliente->documento }}
                        </p>
                    @endif

                    @if($venta->celular_cliente)
                        <p class="mb-2">
                            <strong><i class="fas fa-phone text-primary"></i> Celular:</strong> 
                            {{ $venta->celular_cliente }}
                        </p>
                    @endif

                    <p class="mb-2">
                        <strong><i class="fas fa-user-tie text-primary"></i> Vendedor:</strong> 
                        {{ $venta->usuario?->name ?? 'N/A' }}
                    </p>
                </div>

                <div class="col-md-6">
                    <p class="mb-2">
                        <strong><i class="fas fa-calendar text-primary"></i> Fecha:</strong> 
                        {{ $venta->created_at?->format('d/m/Y H:i') }}
                    </p>
                    
                    <p class="mb-2">
                        <strong><i class="fas fa-money-bill-wave text-success"></i> Total:</strong> 
                        <span class="fs-5 fw-bold text-success">S/ {{ number_format($venta->total, 2) }}</span>
                    </p>
                    
                    <p class="mb-2">
                        <strong><i class="fas fa-wallet text-primary"></i> Tipo de pago:</strong> 
                        <span class="badge {{ $venta->tipo_pago == 'contado' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($venta->tipo_pago) }}
                        </span>
                    </p>
                    
                    <p class="mb-0">
                        <strong><i class="fas fa-credit-card text-primary"></i> Método de pago:</strong> 
                        {{ ucfirst($venta->metodo_pago ?? '-') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- === DETALLES DEL CRÉDITO (SI APLICA) === --}}
    {{-- ========================================================== --}}
    @if($venta->esCredito())
        <div class="card shadow-sm border-0 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Detalles del Crédito</h5>
            </div>
            <div class="card-body">
                @if($venta->cuotas->count() > 0)
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Monto por cuota:</strong> 
                        S/ {{ number_format($venta->cuotas->first()->monto, 2) }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Fecha de Vencimiento</th>
                                <th>Monto</th>
                                <th>Monto Pagado</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->cuotas as $cuota)
                                <tr>
                                    <td class="text-center fw-bold">{{ $cuota->numero_cuota }}</td>
                                    <td>
                                        <i class="fas fa-calendar text-muted"></i>
                                        {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}
                                    </td>
                                    <td class="fw-bold">S/ {{ number_format($cuota->monto, 2) }}</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            step="0.01"
                                            class="form-control form-control-sm monto-cuota"
                                            data-id="{{ $cuota->id }}"
                                            value="{{ $cuota->monto_pagado > 0 ? $cuota->monto_pagado : '' }}"
                                            {{ $cuota->pagada ? 'disabled' : '' }}
                                            placeholder="0.00"
                                            style="max-width: 130px;"
                                        >
                                    </td>
                                    <td class="text-center">
                                        @if($cuota->pagada)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> Pagada
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock"></i> Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$cuota->pagada)
                                            <button 
                                                class="btn btn-sm btn-primary btn-pagar" 
                                                data-id="{{ $cuota->id }}">
                                                <i class="fas fa-check"></i> Registrar pago
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="fas fa-lock"></i> Completada
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Estado general del crédito --}}
                <div class="mt-3">
                    @if ($venta->estaPagada())
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle"></i> 
                            <strong>Venta completamente pagada.</strong>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <strong>Venta con cuotas pendientes.</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ========================================================== --}}
    {{-- === PRODUCTOS VENDIDOS === --}}
    {{-- ========================================================== --}}
    <div class="card shadow-sm border-0 mb-4" data-aos="fade-up" data-aos-delay="200">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-box"></i> Productos Vendidos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unitario</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->detalles as $detalle)
                            <tr>
                                <td>
                                    <strong>{{ $detalle->producto->nombre }}</strong>
                                    @if($detalle->producto->codigo_barras)
                                        <br><small class="text-muted">{{ $detalle->producto->codigo_barras }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $detalle->cantidad }}</span>
                                </td>
                                <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="text-end fw-bold">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                            <td class="text-end fw-bold text-success fs-5">
                                S/ {{ number_format($venta->total, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- ========================================================== --}}
    {{-- === BOTÓN VOLVER === --}}
    {{-- ========================================================== --}}
    <a href="{{ route('dashboard.ventas.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Volver a Ventas
    </a>
</div>

{{-- ========================================================== --}}
{{-- === SCRIPTS === --}}
{{-- ========================================================== --}}
@push('scripts')
<script>
    // ✅ CSRF Token para todas las peticiones
    const csrfToken = '{{ csrf_token() }}';

    // ✅ RUTA para descargar PDF según el stand
    const rutaDescargar = "{{ $venta->stand_id == 1 
        ? route('stands.stand1.ventas.descargar-comprobante', ['id' => $venta->id]) 
        : route('stands.stand2.ventas.descargar-comprobante', ['id' => $venta->id]) }}";

    // -----------------------------------------------------
    // 1. FUNCIÓN PARA DESCARGAR PDF
    // -----------------------------------------------------
    function descargarComprobante() {
        window.location.href = rutaDescargar;
    }

    // -----------------------------------------------------
    // 2. LÓGICA DE PAGO DE CUOTAS
    // -----------------------------------------------------
    document.querySelectorAll('.btn-pagar').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const input = document.querySelector(`.monto-cuota[data-id='${id}']`);
            const monto = parseFloat(input.value);

            if (!monto || monto <= 0) {
                alert('❌ Ingrese un monto válido antes de registrar el pago.');
                return;
            }

            // Confirmación
            if (!confirm(`¿Confirmar pago de S/ ${monto.toFixed(2)}?`)) {
                return;
            }

            // Deshabilitar botón mientras se procesa
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

            try {
                // Detectar stand para usar la ruta correcta
                const standId = {{ $venta->stand_id }};
                const ruta = standId == 1 
                    ? `/stand1/ventas/cuota/${id}/pagar`
                    : `/stand2/ventas/cuota/${id}/pagar`;

                const res = await fetch(ruta, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ monto_pagado: monto })
                });

                const data = await res.json();

                if (data.success) {
                    alert('✅ Pago registrado correctamente.');
                    window.location.reload();
                } else {
                    alert('⚠️ ' + (data.error || 'Error al actualizar la cuota.'));
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check"></i> Registrar pago';
                }
            } catch (error) {
                alert('⚠️ Error en la conexión con el servidor.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Registrar pago';
            }
        });
    });
</script>
@endpush
@endsection