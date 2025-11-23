@extends('layouts.dashboard')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container-fluid">
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="fas fa-users-cog text-primary"></i> Gestión de Usuarios
            </h2>
            <p class="text-muted mb-0">Administración de usuarios del sistema y clientes e-commerce</p>
        </div>
        @if($tipoUsuario === 'sistema')
        <a href="{{ route('dashboard.usuarios.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Nuevo Usuario Sistema
        </a>
        @endif
    </div>

    {{-- PESTAÑAS: SISTEMA vs CLIENTES E-COMMERCE --}}
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ $tipoUsuario === 'sistema' ? 'active' : '' }}" 
               href="{{ route('dashboard.usuarios.index', ['tipo' => 'sistema']) }}">
                <i class="fas fa-user-tie"></i> Usuarios del Sistema
                <span class="badge bg-primary ms-2">{{ \App\Models\User::whereHas('roles')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tipoUsuario === 'clientes' ? 'active' : '' }}" 
               href="{{ route('dashboard.usuarios.index', ['tipo' => 'clientes']) }}">
                <i class="fas fa-shopping-bag"></i> Clientes E-commerce
                <span class="badge bg-success ms-2">{{ \App\Models\User::whereDoesntHave('roles')->count() }}</span>
            </a>
        </li>
    </ul>

    {{-- ESTADÍSTICAS --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                    <i class="fas fa-users"></i>
                </div>
                <p>Total {{ $tipoUsuario === 'sistema' ? 'Usuarios' : 'Clientes' }}</p>
                <h3>{{ number_format($stats['total']) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-user-check"></i>
                </div>
                <p>Activos</p>
                <h3>{{ number_format($stats['activos']) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-user-times"></i>
                </div>
                <p>Inactivos</p>
                <h3>{{ number_format($stats['inactivos']) }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <p>Nuevos Este Mes</p>
                <h3>{{ number_format($stats['nuevos_mes']) }}</h3>
            </div>
        </div>
    </div>

    @if($tipoUsuario === 'clientes')
    {{-- ESTADÍSTICAS ADICIONALES PARA CLIENTES E-COMMERCE --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="stat-card">
                <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <p>Clientes con Compras</p>
                <h3>{{ number_format($stats['con_compras'] ?? 0) }}</h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <div class="icon" style="background: rgba(156, 163, 175, 0.1); color: #9ca3af;">
                    <i class="fas fa-user-slash"></i>
                </div>
                <p>Sin Compras</p>
                <h3>{{ number_format($stats['sin_compras'] ?? 0) }}</h3>
            </div>
        </div>
    </div>
    @endif

    {{-- FILTROS --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.usuarios.index') }}">
                <input type="hidden" name="tipo" value="{{ $tipoUsuario }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Buscar por nombre, email, DNI o teléfono..." value="{{ request('buscar') }}">
                    </div>
                    
                    @if($tipoUsuario === 'sistema')
                    <div class="col-md-3">
                        <select name="rol" class="form-select">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('rol') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="col-md-{{ $tipoUsuario === 'sistema' ? '3' : '5' }}">
                        <select name="active" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA DE USUARIOS --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ $tipoUsuario === 'sistema' ? 'Usuario' : 'Cliente' }}</th>
                            <th>Email</th>
                            <th>DNI</th>
                            <th>Teléfono</th>
                            @if($tipoUsuario === 'sistema')
                            <th>Rol</th>
                            <th>Stand</th>
                            @else
                            <th>Compras</th>
                            <th>Total Gastado</th>
                            @endif
                            <th>Registro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3" style="width: 40px; height: 40px; background: {{ $tipoUsuario === 'sistema' ? '#4f46e5' : '#10b981' }}; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                                        {{ strtoupper(substr($usuario->name, 0, 1)) }}
                                    </div>
                                    <strong>{{ $usuario->name }}</strong>
                                </div>
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->dni ?? 'N/A' }}</td>
                            <td>{{ $usuario->telefono ?? 'N/A' }}</td>
                            
                            @if($tipoUsuario === 'sistema')
                            <td>
                                <span class="badge bg-primary">
                                    {{ $usuario->roles->first()->name ?? 'Sin rol' }}
                                </span>
                            </td>
                            <td>
                                @if($usuario->stand_id)
                                    <span class="badge bg-info">Stand {{ $usuario->stand_id }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            @else
                            {{-- Estadísticas de cliente e-commerce --}}
                            <td>
                                @php
                                    // Buscar si existe un cliente con el mismo email/documento
                                    $clienteAsociado = \App\Models\Cliente::where('email', $usuario->email)
                                        ->orWhere('documento', $usuario->dni)
                                        ->first();
                                    $totalCompras = $clienteAsociado ? $clienteAsociado->ventas->count() : 0;
                                @endphp
                                <span class="badge bg-{{ $totalCompras > 0 ? 'success' : 'secondary' }}">
                                    {{ $totalCompras }} compras
                                </span>
                            </td>
                            <td>
                                @php
                                    $totalGastado = $clienteAsociado ? $clienteAsociado->ventas->sum('total') : 0;
                                @endphp
                                <strong>S/ {{ number_format($totalGastado, 2) }}</strong>
                            </td>
                            @endif
                            
                            <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-estado" type="checkbox" 
                                           data-id="{{ $usuario->id }}"
                                           {{ $usuario->active ? 'checked' : '' }}
                                           {{ $usuario->id == auth()->id() ? 'disabled' : '' }}>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('dashboard.usuarios.show', $usuario->id) }}" 
                                   class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($tipoUsuario === 'sistema')
                                <a href="{{ route('dashboard.usuarios.edit', $usuario->id) }}" 
                                   class="btn btn-sm btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                
                                @if($usuario->id != auth()->id())
                                    <form action="{{ route('dashboard.usuarios.destroy', $usuario->id) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('¿Eliminar este {{ $tipoUsuario === 'sistema' ? 'usuario' : 'cliente' }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $tipoUsuario === 'sistema' ? '10' : '10' }}" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                No se encontraron {{ $tipoUsuario === 'sistema' ? 'usuarios' : 'clientes' }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($usuarios->hasPages())
        <div class="card-footer">
            {{ $usuarios->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Toggle estado activo/inactivo
document.querySelectorAll('.toggle-estado').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const id = this.dataset.id;
        
        fetch(`/dashboard/usuarios/${id}/toggle-estado`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                this.checked = !this.checked;
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(() => {
            this.checked = !this.checked;
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al cambiar estado'
            });
        });
    });
});
</script>
@endpush
@endsection