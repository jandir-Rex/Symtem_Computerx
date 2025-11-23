@extends('layouts.appe')
@section('title', 'Mis Pedidos')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3><i class="fas fa-shopping-bag"></i> Mis Pedidos</h3>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-box-open fa-5x text-muted mb-4"></i>
                    <h4>Aún no tienes pedidos</h4>
                    <p class="text-muted">Cuando hagas una compra, aparecerán aquí</p>
                    <a href="/" class="btn btn-primary mt-3">Seguir Comprando</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection