@extends('layouts.app')

@section('title', 'Detalle de Venta - Stand 2')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Detalle de la Venta #{{ $venta->id }}</h1>

    {{-- ========================================================== --}}
    {{-- === BOTONES DE IMPRESIÓN (PARTE SUPERIOR) === --}}
    {{-- ========================================================== --}}
    <div class="mb-4 d-flex justify-content-start flex-wrap gap-2">
        {{-- Botón: Descargar/Imprimir PDF --}}
        <button 
            class="btn btn-primary" 
            onclick="descargarComprobante()"
        >
            <i class="fas fa-print"></i> Descargar / Imprimir PDF
        </button>

        {{-- Contenedor para mensajes de estado --}}
        <div id="status-message" class="w-100 mt-2"></div>
    </div>
    {{-- ========================================================== --}}

    <div class="card shadow-sm border-light">
        <div class="card-body">
            <p><strong>Cliente:</strong> {{ $venta->cliente?->nombre ?? 'Sin cliente' }}</p>
            
            @if($venta->celular_cliente)
                <p><strong>Celular del cliente:</strong> {{ $venta->celular_cliente }}</p>
            @endif

            <p><strong>Fecha:</strong> {{ $venta->created_at?->format('d/m/Y H:i') }}</p>
            <p><strong>Total:</strong> S/ {{ number_format($venta->total, 2) }}</p>
            <p><strong>Tipo de pago:</strong> {{ ucfirst($venta->tipo_pago) }}</p>
            <p><strong>Método de pago:</strong> {{ ucfirst($venta->metodo_pago ?? '-') }}</p>
        </div>
    </div>

    {{-- Si la venta es a crédito, mostrar las cuotas --}}
    @if($venta->esCredito())
        <div class="card mt-4 shadow-sm border-light">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-primary fw-bold"><i class="fas fa-calendar-alt"></i> Detalles del Crédito</h5>
            </div>
            <div class="card-body">
                @if($venta->cuotas->count() > 0)
                   <p><strong>Monto por cuota:</strong> 
                    S/ {{ number_format($venta->cuotas->first()->monto, 2) }}
                   </p>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered align-middle mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Fecha de Vencimiento</th>
                                <th>Monto</th>
                                <th>Monto Pagado</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($venta->cuotas as $cuota)
                                <tr>
                                    <td>{{ $cuota->numero_cuota }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }}</td>
                                    <td>S/ {{ number_format($cuota->monto, 2) }}</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            step="0.01"
                                            class="form-control form-control-sm monto-cuota"
                                            data-id="{{ $cuota->id }}"
                                            value="{{ $cuota->monto_pagado > 0 ? $cuota->monto_pagado : '' }}"
                                            {{ $cuota->pagada ? 'disabled' : '' }}
                                            style="max-width: 120px;"
                                        >
                                    </td>
                                    <td>
                                        @if($cuota->pagada)
                                            <span class="badge bg-success">Pagada</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$cuota->pagada)
                                            <button 
                                                class="btn btn-sm btn-primary btn-pagar" 
                                                data-id="{{ $cuota->id }}">
                                                Registrar pago
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                Completada
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Estado general --}}
                <div class="mt-3">
                    @if ($venta->estaPagada())
                        <div class="alert alert-success mb-0">
                            💰 <strong>Venta completamente pagada.</strong>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            ⚠️ <strong>Venta con cuotas pendientes.</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Productos vendidos --}}
    <h4 class="mt-4">Productos vendidos</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover mt-3 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td class="text-center">{{ $detalle->cantidad }}</td>
                        <td>S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>S/ {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <a href="{{ route('stands.stand2.ventas.index') }}" class="btn btn-outline-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Volver a Ventas
    </a>
</div>

{{-- === SCRIPT: Funciones de Pago y PDF === --}}
<script>
    // ✅ CSRF Token para todas las peticiones
    const csrfToken = '{{ csrf_token() }}';

    // ✅ RUTA para descargar PDF (👈 CAMBIO: stand2)
    const rutaDescargar = "{{ route('stands.stand2.ventas.descargar-comprobante', ['id' => $venta->id]) }}";

    // -----------------------------------------------------
    // 1. FUNCIÓN PARA DESCARGAR PDF
    // -----------------------------------------------------

    /**
     * ⬇️ Descargar el PDF directamente
     */
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

            try {
                // 👈 CAMBIO: ruta de stand1 a stand2
                const res = await fetch(`/stand2/ventas/cuota/${id}/pagar`, {
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
                }
            } catch (error) {
                alert('⚠️ Error en la conexión con el servidor.');
            }
        });
    });
</script>
@endsection