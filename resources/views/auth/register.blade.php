@extends('layouts.appe')

@section('title', 'Crear cuenta | Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Crear cuenta</h2>
                        <p class="text-muted">Regístrate para acceder a todas las funciones</p>
                    </div>

                    <!-- Mensajes de error -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0" style="font-size: 0.875rem;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Mensaje de éxito -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre completo *</label>
                            <input type="text" 
                                class="form-control @error('name') is-invalid @enderror" 
                                id="name" 
                                name="name" 
                                required 
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico *</label>
                            <input type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                required 
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dni" class="form-label">DNI *</label>
                            <input type="text" 
                                class="form-control @error('dni') is-invalid @enderror" 
                                id="dni" 
                                name="dni" 
                                required 
                                value="{{ old('dni') }}"
                                placeholder="Ej: 12345678">
                            @error('dni')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña *</label>
                            <input type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                id="password" 
                                name="password" 
                                required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar contraseña *</label>
                            <input type="password" 
                                class="form-control" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-user-plus me-2"></i>CREAR CUENTA
                        </button>

                        <div class="text-center">
                            <p class="mb-0">¿Ya tienes cuenta?</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary mt-2">
                                <i class="fas fa-sign-in-alt me-2"></i>INICIAR SESIÓN
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection