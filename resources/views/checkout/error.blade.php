
@extends('layouts.appe')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Error en el Proceso de Pago</h4>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                    <h3 class="mt-3">{{ session('error', 'Ocurrió un error') }}</h3>
                    <p class="text-muted">Si el dinero fue debitado, por favor contacta a soporte.</p>
                    <div class="mt-4">
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary">Intentar Nuevamente</a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Volver al Inicio</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection