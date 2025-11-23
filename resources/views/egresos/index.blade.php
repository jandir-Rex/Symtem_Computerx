{{-- resources/views/egresos/index.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Listado de Egresos')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-dolly-flatbed"></i> Listado de Compras y Gastos (Egresos)</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Registro de Comprobantes</h6>
            <a href="{{ route('dashboard.egresos.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nuevo Egreso
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha Emisión</th>
                            <th>Tipo Doc.</th>
                            <th>Proveedor</th>
                            <th>Descripción</th>
                            <th class="text-end">Monto Total</th>
                            <th class="text-center">Crédito Fiscal</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($egresos as $egreso)
                            @php
                                // Cálculo Inverso para mostrar detalles (0 si no es Factura)
                                $is_factura = str_contains(strtoupper($egreso->documento_tipo), 'FACTURA');
                                $subtotal = $egreso->total / 1.18;
                                $igv_credito = $egreso->total - $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $egreso->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($egreso->fecha_emision)->format('Y-m-d') }}</td>
                                <td><span class="badge bg-{{ $is_factura ? 'primary' : 'secondary' }}">{{ $egreso->documento_tipo }}</span></td>
                                <td>{{ $egreso->proveedor->nombre ?? 'N/A' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($egreso->descripcion, 50) }}</td>
                                <td class="text-end">S/ <strong>{{ number_format($egreso->total, 2) }}</strong></td>
                                <td class="text-center text-success">
                                    @if ($is_factura)
                                        S/ <strong>{{ number_format($igv_credito, 2) }}</strong>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i> No Aplica
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('dashboard.egresos.edit', $egreso) }}" class="btn btn-info btn-sm" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form action="{{ route('dashboard.egresos.destroy', $egreso) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este egreso? Esta acción afectará el cálculo fiscal.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay egresos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $egresos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection