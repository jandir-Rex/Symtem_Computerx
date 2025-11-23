@extends('layouts.app')

@section('title', 'Recuperar contraseña | Company Computer')

@section('content')
<div class="login-container">
    <h1>¿Olvidaste tu contraseña?</h1>

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="login-form">
        @csrf

        <div class="form-group">
            <label for="email">Correo electrónico *</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}">
        </div>

        <button type="submit" class="login-button">ENVIAR ENLACE DE RECUPERACIÓN</button>

        <div class="signup-link">
            <p><a href="{{ route('login') }}">← Volver al login</a></p>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush