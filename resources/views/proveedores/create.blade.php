@extends('layouts.dashboard')

@section('title', 'Registrar Nuevo Proveedor')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm" data-aos="fade-up">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Registrar Nuevo Proveedor
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('dashboard.proveedores.store') }}" method="POST">
                        @csrf
                        @include('proveedores.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection