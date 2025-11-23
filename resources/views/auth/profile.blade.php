@extends('layouts.appe')
@section('title', 'Mi Perfil')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3><i class="fas fa-user"></i> Mi Perfil</h3>
                </div>
                <div class="card-body">
                    <p><strong>Nombre:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    <p><strong>DNI:</strong> {{ Auth::user()->dni }}</p>
                    <p><strong>Registrado el:</strong> {{ Auth::user()->created_at->format('d/m/Y') }}</p>
                    
                    <a href="{{ route('logout') }}" 
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="btn btn-danger mt-3">
                        Cerrar Sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection