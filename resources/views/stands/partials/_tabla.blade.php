<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light text-center">
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Tipo de Venta</th>
                <th>Total</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="ventasBody">
            @forelse ($ventas as $venta)
                <tr>
                    <td class="text-center fw-bold">{{ $venta->id }}</td>
                    <td>{{ $venta->cliente?->nombre ?? 'Sin cliente' }}</td>

                    <td class="text-center">
                        @if($venta->tipo_pago === 'contado')
                            <span class="badge bg-success">Contado</span>
                        @else
                            <span class="badge bg-warning text-dark">Crédito</span>
                        @endif
                    </td>

                    <td class="text-center fw-semibold">S/ {{ number_format($venta->total, 2) }}</td>
                    <td class="text-center">{{ $venta->created_at->format('d/m/Y') }}</td>

                    <td class="text-center">
                        @if($venta->pagado)
                            <span class="badge bg-success">Pagada</span>
                        @else
                            <span class="badge bg-danger">Pendiente</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <a href="{{ route('stands.stand1.ventas.show', $venta->id) }}" 
                           class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        No se encontraron ventas con esos filtros.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div id="paginacion" class="mt-3 d-flex justify-content-center">
    {{ $ventas->appends(request()->query())->links() }}
</div>
