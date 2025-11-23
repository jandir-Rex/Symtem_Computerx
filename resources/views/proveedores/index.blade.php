@extends('layouts.dashboard')

@section('title', 'Gestión de Proveedores')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-down">
        <h1 class="h3 mb-0">
            <i class="fas fa-truck text-primary"></i> Gestión de Proveedores
        </h1>
        <a href="{{ route('dashboard.proveedores.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Proveedor
        </a>
    </div>

    <div class="card shadow-sm" data-aos="fade-up">
        <div class="card-header bg-white border-bottom">
            <form action="{{ route('dashboard.proveedores.index') }}" method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por nombre o RUC..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold">RUC</th>
                            <th class="fw-semibold">Nombre / Razón Social</th>
                            <th class="fw-semibold">Teléfono</th>
                            <th class="fw-semibold">Email</th>
                            <th class="fw-semibold text-center">Egresos</th>
                            <th class="fw-semibold text-center" style="width: 150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $proveedor)
                            <tr>
                                <td><strong class="text-primary">{{ $proveedor->ruc }}</strong></td>
                                <td>{{ $proveedor->nombre }}</td>
                                <td>
                                    @if($proveedor->telefono)
                                        <i class="fas fa-phone text-muted me-1"></i>{{ $proveedor->telefono }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($proveedor->email)
                                        <i class="fas fa-envelope text-muted me-1"></i>{{ $proveedor->email }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $proveedor->egresos->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('dashboard.proveedores.edit', $proveedor) }}" class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" title="Eliminar" onclick="confirmarEliminacion({{ $proveedor->id }}, '{{ $proveedor->nombre }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $proveedor->id }}" action="{{ route('dashboard.proveedores.destroy', $proveedor) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                                    <p class="mb-0">No hay proveedores registrados</p>
                                    <a href="{{ route('dashboard.proveedores.create') }}" class="btn btn-sm btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Crear el primero
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($proveedores->hasPages())
            <div class="card-footer bg-white border-top">
                {{ $proveedores->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function confirmarEliminacion(id, nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `Se eliminará el proveedor: <strong>${nombre}</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
@endsection