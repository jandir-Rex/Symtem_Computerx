<div class="row">
    {{-- Información del Cliente --}}
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-user"></i> Información del Cliente
                </h6>
                <p><strong>Nombre:</strong> {{ $venta->cliente->nombre ?? 'Sin cliente' }}</p>
                <p><strong>Documento:</strong> {{ $venta->cliente->documento ?? 'N/A' }}</p>
                <p><strong>Celular:</strong> {{ $venta->celular_cliente ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    {{-- Información de la Venta --}}
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold text-primary mb-3">
                    <i class="fas fa-receipt"></i> Información de la Venta
                </h6>
                <p><strong>N° Comprobante:</strong> {{ $venta->numero_comprobante ?? 'N/A' }}</p>
                <p><strong>Tipo:</strong> 
                    <span class="badge bg-{{ $venta->tipo_pago === 'contado' ? 'success' : 'warning' }}">
                        {{ ucfirst($venta->tipo_pago) }}
                    </span>
                </p>
                <p><strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Vendedor:</strong> {{ $venta->usuario->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Detalle de Productos --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h6 class="fw-bold text-primary mb-3">
            <i class="fas fa-box"></i> Productos
        </h6>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                            <td class="text-center">{{ $detalle->cantidad }}</td>
                            <td class="text-end">S/ {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="text-end">S/ {{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                        <td class="text-end">S/ {{ number_format($venta->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">IGV (18%):</td>
                        <td class="text-end">S/ {{ number_format($venta->igv, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                        <td class="text-end fw-bold text-primary">S/ {{ number_format($venta->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Cuotas (si es a crédito) --}}
@if($venta->tipo_pago === 'credito' && $venta->cuotas->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="fw-bold text-primary mb-3">
                <i class="fas fa-calendar-alt"></i> Plan de Cuotas
            </h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Cuota</th>
                            <th>Monto</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($venta->cuotas as $cuota)
                            <tr>
                                <td>Cuota {{ $cuota->numero_cuota }}</td>
                                <td>S/ {{ number_format($cuota->monto, 2) }}</td>
                                <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                                <td>
                                    @if($cuota->pagada)
                                        <span class="badge bg-success">Pagada</span>
                                    @else
                                        <span class="badge bg-danger">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif