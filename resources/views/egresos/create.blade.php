{{-- resources/views/egresos/create.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Registro de Nuevo Egreso')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-file-invoice-dollar"></i> Registrar Nuevo Egreso (Gasto/Compra)</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detalles del Comprobante</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.egresos.store') }}" method="POST">
                @csrf
                @include('egresos.form')
            </form>
        </div>
    </div>
</div>
@endsection