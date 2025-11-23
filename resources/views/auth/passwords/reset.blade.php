@extends('layouts.app')

@section('title', 'Restablecer contraseña | Company Computer')

@section('content')
<div class="login-container">
    <h1>Restablecer contraseña</h1>

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

    <form method="POST" action="{{ route('password.update') }}" class="login-form">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="form-group">
            <label for="password">Nueva contraseña *</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar nueva contraseña *</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="login-button">ACTUALIZAR CONTRASEÑA</button>

        <div class="signup-link">
            <p><a href="{{ route('login') }}">← Volver al login</a></p>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush