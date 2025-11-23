@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="container">
    <h3 class="fw-bold mb-4"><i class="fas fa-edit"></i> Editar Producto</h3>

    <form action="{{ route('almacen.update', $producto) }}" method="POST" enctype="multipart/form-data" class="card shadow-sm p-4">
        @csrf
        @method('PUT')
        @include('almacen.partials.form', ['modo' => 'editar'])
    </form>
</div>
@endsection
