@extends('layouts.app')

@section('title', 'Nuevo Producto')

@section('content')
<div class="container">
    <h3 class="fw-bold mb-4"><i class="fas fa-plus-circle"></i> Nuevo Producto</h3>

    <form action="{{ route('almacen.store') }}" method="POST" enctype="multipart/form-data" class="card shadow-sm p-4">
        @csrf
        @include('almacen.partials.form', ['modo' => 'crear'])
    </form>
</div>
@endsection
