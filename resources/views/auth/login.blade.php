@extends('layouts.appe')

@section('title', 'Iniciar sesión | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Iniciar sesión</h2>
                        <p class="text-muted">Ingresa tus credenciales para acceder</p>
                    </div>

                    {{-- ✅ Mensaje de éxito --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- ✅ Mensajes de error --}}
                    @if ($errors->has('email'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('email') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->has('password'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first('password') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico *</label>
                            <input type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                required 
                                placeholder="ejemplo@correo.com" 
                                value="{{ old('email') }}"
                                autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <div class="input-group">
                                <input type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    placeholder="••••••••">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Recuérdame
                                </label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-decoration-none">
                                ¿Olvidó su contraseña?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>INGRESAR
                        </button>

                        <div class="text-center">
                            <p class="mb-0">¿Aún no tienes cuenta?</p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-user-plus me-2"></i>CREAR CUENTA
                            </a>
                        </div>
                    </form>

                    {{-- ✅ Info box para usuarios --}}
                    <div class="alert alert-info mt-3 mb-0" role="alert">
                        <small>
                            <strong><i class="fas fa-info-circle me-1"></i> Acceso según tipo de usuario:</strong><br>
                            <span class="d-block mt-1">✅ <strong>Clientes:</strong> Registrarse para comprar</span>
                            <span class="d-block">🔐 <strong>Personal:</strong> Usar credenciales asignadas</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script para mostrar/ocultar contraseña --}}
@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection