@extends('layouts.appe')

@section('title', $titulo . ' - Company Computer')
@section('body-class', 'category-page')

@section('content')
<div class="container py-5">
    {{-- Encabezado --}}
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-dark">{{ $titulo }}</h1>
            <p class="text-muted">{{ $productos->total() }} productos encontrados</p>
        </div>
    </div>

    {{-- Grid de Productos --}}
    <div class="row g-4">
        @forelse($productos as $producto)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card product-card h-100 shadow-sm hover-shadow border-0">
                {{-- Imagen --}}
                <a href="{{ route('product.show', [$categoria ?? $producto->categoria_url, $producto->slug]) }}">
                    <img src="{{ $producto->imagen_url }}" 
                         class="card-img-top" 
                         alt="{{ $producto->nombre }}"
                         style="height: 200px; object-fit: cover;">
                </a>

                {{-- Badge de Stock --}}
                @if($producto->stock <= $producto->stock_minimo)
                    <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                        ⚠️ Últimas unidades
                    </span>
                @endif

                {{-- Contenido --}}
                <div class="card-body d-flex flex-column text-center">
                    <h6 class="card-title text-truncate mb-2" title="{{ $producto->nombre }}">
                        {{ $producto->nombre }}
                    </h6>
                    
                    @if($producto->marca)
                    <small class="text-muted">{{ $producto->marca }}</small>
                    @endif

                    <div class="mt-auto">
                        <p class="h5 text-dark fw-bold mb-3">
                            S/ {{ number_format($producto->precio_venta, 2) }}
                        </p>

                        <div class="d-grid gap-2">
                            <a href="{{ route('product.show', [$categoria ?? $producto->categoria_url, $producto->slug]) }}" 
                               class="btn btn-outline-dark btn-sm">
                                <i class="fas fa-eye"></i> Ver Producto
                            </a>
                            <button class="btn btn-dark btn-sm add-to-cart" 
                                    data-id="{{ $producto->id }}"
                                    data-name="{{ $producto->nombre }}"
                                    data-price="{{ $producto->precio_venta }}"
                                    data-image="{{ $producto->imagen_url }}"
                                    data-category="{{ $producto->categoria }}"
                                    data-stock="{{ $producto->stock }}">
                                <i class="fas fa-cart-plus"></i> Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4>No hay productos disponibles en esta categoría</h4>
            <p class="text-muted">Vuelve pronto para ver nuevos productos</p>
            <a href="{{ route('home') }}" class="btn btn-dark mt-3">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
        </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    @if($productos->hasPages())
    <div class="row mt-5">
        <div class="col-12 d-flex justify-content-center">
            {{ $productos->links() }}
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.product-card {
    transition: all 0.3s ease;
}
.product-card:hover {
    transform: translateY(-5px);
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.btn-dark {
    background-color: #1a1a1a;
    border-color: #1a1a1a;
}
.btn-dark:hover {
    background-color: #000;
    border-color: #000;
}
.btn-outline-dark {
    color: #1a1a1a;
    border-color: #1a1a1a;
}
.btn-outline-dark:hover {
    background-color: #1a1a1a;
    border-color: #1a1a1a;
    color: white;
}
</style>
@endpush
@endsection