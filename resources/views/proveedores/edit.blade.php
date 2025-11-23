@extends('layouts.dashboard')

@section('title', 'Editar Proveedor')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm" data-aos="fade-up">
                <div class="card-header bg-warning">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-edit"></i> Editar Proveedor: {{ $proveedor->nombre }}
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('dashboard.proveedores.update', $proveedor) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('proveedores.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection