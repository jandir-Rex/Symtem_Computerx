{{-- resources/views/egresos/edit.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Editar Egreso N° ' . $egreso->id)

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-edit"></i> Editar Egreso N° {{ $egreso->id }}</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Modificar Detalles</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('dashboard.egresos.update', $egreso) }}" method="POST">
                @csrf
                @method('PUT')
                @include('egresos.form')
            </form>
        </div>
    </div>
</div>
@endsection