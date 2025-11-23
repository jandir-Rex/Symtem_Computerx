@extends('layouts.dashboard')

@section('title', 'Editar Usuario')

@section('content')
<div class="container-fluid px-4">
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                <i class="fas fa-user-edit text-primary"></i> 
                Editar Usuario
            </h4>
        </div>
        <div>
            <a href="{{ route('dashboard.usuarios.show', $usuario->id) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>
    </div>

    {{-- FORMULARIO --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header py-2 bg-white">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-edit text-primary"></i> Información del Usuario
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.usuarios.update', $usuario->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- INFORMACIÓN BÁSICA --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-bold">
                                    <i class="fas fa-user text-muted"></i> Nombre Completo *
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $usuario->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">
                                    <i class="fas fa-envelope text-muted"></i> Email *
                                </label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $usuario->email) }}" 
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- DNI Y TELÉFONO --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="dni" class="form-label fw-bold">
                                    <i class="fas fa-id-card text-muted"></i> DNI
                                </label>
                                <input type="text" 
                                       class="form-control @error('dni') is-invalid @enderror" 
                                       id="dni" 
                                       name="dni" 
                                       value="{{ old('dni', $usuario->dni) }}" 
                                       maxlength="20">
                                @error('dni')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label fw-bold">
                                    <i class="fas fa-phone text-muted"></i> Teléfono
                                </label>
                                <input type="text" 
                                       class="form-control @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono', $usuario->telefono) }}" 
                                       maxlength="20">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- DIRECCIÓN --}}
                        <div class="mb-3">
                            <label for="direccion" class="form-label fw-bold">
                                <i class="fas fa-map-marker-alt text-muted"></i> Dirección
                            </label>
                            <input type="text" 
                                   class="form-control @error('direccion') is-invalid @enderror" 
                                   id="direccion" 
                                   name="direccion" 
                                   value="{{ old('direccion', $usuario->direccion) }}">
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ROL Y STAND --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rol" class="form-label fw-bold">
                                    <i class="fas fa-user-tag text-muted"></i> Rol *
                                </label>
                                <select class="form-select @error('rol') is-invalid @enderror" 
                                        id="rol" 
                                        name="rol" 
                                        required>
                                    <option value="">Seleccionar rol...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" 
                                                {{ old('rol', $usuario->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="stand_id" class="form-label fw-bold">
                                    <i class="fas fa-store text-muted"></i> Stand (opcional)
                                </label>
                                <select class="form-select @error('stand_id') is-invalid @enderror" 
                                        id="stand_id" 
                                        name="stand_id">
                                    <option value="">Sin stand asignado</option>
                                    @foreach($stands as $stand)
                                        <option value="{{ $stand }}" 
                                                {{ old('stand_id', $usuario->stand_id) == $stand ? 'selected' : '' }}>
                                            Stand {{ $stand }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('stand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- CAMBIAR CONTRASEÑA (OPCIONAL) --}}
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Cambiar Contraseña (Opcional)</strong>
                            <p class="mb-0 small">Deja estos campos vacíos si no deseas cambiar la contraseña.</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">
                                    <i class="fas fa-lock text-muted"></i> Nueva Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       minlength="6">
                                <small class="text-muted">Mínimo 6 caracteres</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-bold">
                                    <i class="fas fa-lock text-muted"></i> Confirmar Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       minlength="6">
                            </div>
                        </div>

                        {{-- ESTADO --}}
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="active" 
                                       name="active" 
                                       value="1"
                                       {{ old('active', $usuario->active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="active">
                                    <i class="fas fa-toggle-on text-success"></i> Usuario Activo
                                </label>
                                <small class="d-block text-muted">Los usuarios inactivos no pueden iniciar sesión</small>
                            </div>
                        </div>

                        {{-- BOTONES --}}
                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <a href="{{ route('dashboard.usuarios.show', $usuario->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- INFORMACIÓN ADICIONAL --}}
            <div class="card shadow-sm mt-3">
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Creado</small>
                            <strong>{{ $usuario->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Última Actualización</small>
                            <strong>{{ $usuario->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">ID de Usuario</small>
                            <strong>#{{ $usuario->id }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Validación en tiempo real
document.getElementById('password').addEventListener('input', function() {
    const pass = this.value;
    const confirm = document.getElementById('password_confirmation');
    
    if (pass.length > 0 && pass.length < 6) {
        this.setCustomValidity('La contraseña debe tener al menos 6 caracteres');
    } else {
        this.setCustomValidity('');
    }
    
    if (confirm.value && pass !== confirm.value) {
        confirm.setCustomValidity('Las contraseñas no coinciden');
    } else {
        confirm.setCustomValidity('');
    }
});

document.getElementById('password_confirmation').addEventListener('input', function() {
    const pass = document.getElementById('password').value;
    
    if (this.value && pass !== this.value) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});

// Mostrar/Ocultar stand según el rol
document.getElementById('rol').addEventListener('change', function() {
    const standContainer = document.getElementById('stand_id').closest('.col-md-6');
    const rolValue = this.value.toLowerCase();
    
    if (rolValue.includes('stand')) {
        standContainer.style.display = 'block';
    } else {
        standContainer.style.display = 'none';
        document.getElementById('stand_id').value = '';
    }
});

// Ejecutar al cargar la página
window.addEventListener('DOMContentLoaded', function() {
    const rolSelect = document.getElementById('rol');
    rolSelect.dispatchEvent(new Event('change'));
});
</script>
@endpush

<style>
.form-label {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.form-control,
.form-select {
    font-size: 0.95rem;
}

.card {
    border-radius: 10px;
}

.alert {
    border-radius: 8px;
}

.btn {
    border-radius: 6px;
    font-weight: 600;
}
</style>
@endsection