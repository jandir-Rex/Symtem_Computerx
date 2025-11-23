@extends('layouts.appe')

@section('title', $producto->nombre . ' | Company Computer')
@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Imagen del producto -->
        <div class="col-lg-6">
            <div class="product-image-container">
                <img src="{{ $producto->imagen_url }}" 
                     alt="{{ $producto->nombre }}"
                     loading="eager"
                     decoding="async"
                     fetchpriority="high">
            </div>
        </div>

        <!-- Detalles del producto -->
        <div class="col-lg-6">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/{{ $producto->categoria_url }}">{{ ucfirst($producto->categoria) }}</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($producto->nombre, 40) }}</li>
                </ol>
            </nav>

            <!-- Título -->
            <h1 class="fw-bold">{{ $producto->nombre }}</h1>

            <!-- SKU y Marca -->
            <div class="d-flex gap-3 mb-3">
                @if($producto->codigo_barras)
                    <span class="badge bg-secondary">SKU: {{ $producto->codigo_barras }}</span>
                @endif
                @if($producto->marca)
                    <span class="badge bg-info">{{ $producto->marca }}</span>
                @endif
            </div>

            <!-- Categoría -->
            <p class="text-muted mb-3">
                <strong>Categoría:</strong> 
                <a href="/{{ $producto->categoria_url }}" class="text-decoration-none">{{ ucfirst($producto->categoria) }}</a>
            </p>

            <!-- Precio y stock -->
            <div class="bg-light p-4 rounded mb-4 shadow-sm">
                <p class="fs-1 text-dark fw-bold mb-2">S/ {{ number_format($producto->precio_venta, 2) }}</p>
                <p class="mb-0">
                    @if($producto->stock > 10)
                        <i class="fas fa-check-circle text-success"></i>
                        <span class="text-success fw-bold">Stock disponible</span>
                    @elseif($producto->stock > 0)
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <span class="text-warning fw-bold">Últimas {{ $producto->stock }} unidades</span>
                    @else
                        <i class="fas fa-times-circle text-danger"></i>
                        <span class="text-danger fw-bold">Sin stock</span>
                    @endif
                </p>
            </div>

            <!-- Botones de acción -->
            @if($producto->stock > 0)
                <div class="d-grid gap-2 mb-3">
                    <button class="btn btn-dark btn-lg add-to-cart"
                        data-id="{{ $producto->id }}"
                        data-name="{{ $producto->nombre }}"
                        data-price="{{ $producto->precio_venta }}"
                        data-image="{{ $producto->imagen_url }}"
                        data-category="{{ $producto->categoria }}"
                        data-stock="{{ $producto->stock }}">
                        <i class="fas fa-cart-plus fa-lg me-2"></i> AÑADIR AL CARRITO
                    </button>
                </div>
            @else
                <button class="btn btn-secondary btn-lg w-100 mb-3" disabled>
                    <i class="fas fa-times me-2"></i> SIN STOCK
                </button>
            @endif

            <!-- Descripción -->
            @if($producto->descripcion)
                <div class="mt-4">
                    <h5 class="fw-bold mb-3">Descripción:</h5>
                    <p class="text-muted">{{ $producto->descripcion }}</p>
                </div>
            @endif

            <!-- Garantía y envío -->
            <div class="alert alert-info mt-4">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Garantía:</strong> Producto con garantía de fábrica
            </div>
        </div>
    </div>

    <!-- Productos Relacionados -->
    @if($relacionados->isNotEmpty())
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="fw-bold mb-4">Productos Relacionados</h3>
        </div>
        
        @foreach($relacionados as $rel)
        <div class="col-6 col-md-3 mb-4">
            <div class="card product-card h-100 shadow-sm hover-shadow border-0">
                <a href="{{ route('product.show', [$rel->categoria_url, $rel->slug]) }}" class="product-link">
                    <div class="related-image-container">
                        <img src="{{ $rel->imagen_url }}" 
                             alt="{{ $rel->nombre }}"
                             loading="lazy"
                             decoding="async">
                    </div>
                </a>

                <div class="card-body d-flex flex-column text-center">
                    <h6 class="card-title product-title mb-2" title="{{ $rel->nombre }}">
                        {{ $rel->nombre }}
                    </h6>
                    
                    @if($rel->marca)
                    <small class="text-muted">{{ $rel->marca }}</small>
                    @endif

                    <div class="mt-auto">
                        <p class="h5 text-dark fw-bold mb-3">
                            S/ {{ number_format($rel->precio_venta, 2) }}
                        </p>

                        <div class="d-grid gap-2">
                            <a href="{{ route('product.show', [$rel->categoria_url, $rel->slug]) }}" 
                               class="btn btn-outline-dark btn-sm">
                                <i class="fas fa-eye"></i> Ver Producto
                            </a>
                            <button class="btn btn-dark btn-sm add-to-cart" 
                                    data-id="{{ $rel->id }}"
                                    data-name="{{ $rel->nombre }}"
                                    data-price="{{ $rel->precio_venta }}"
                                    data-image="{{ $rel->imagen_url }}"
                                    data-category="{{ $rel->categoria }}"
                                    data-stock="{{ $rel->stock }}">
                                <i class="fas fa-cart-plus"></i> Añadir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@push('styles')
<style>
/* ========================================
   CONTENEDOR DE IMAGEN PRINCIPAL
======================================== */
.product-image-container {
    width: 100%;
    height: 500px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 3rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.product-image-container img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    /* Optimización para nitidez de imagen */
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    -webkit-backface-visibility: hidden;
    -ms-interpolation-mode: bicubic;
    backface-visibility: hidden;
    -webkit-font-smoothing: antialiased;
    will-change: transform;
}

/* ========================================
   PRODUCTOS RELACIONADOS
======================================== */
.related-image-container {
    width: 100%;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    padding: 1rem;
    overflow: hidden;
}

.related-image-container img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    transition: transform 0.3s ease;
    /* Optimización para nitidez de imagen */
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    -webkit-backface-visibility: hidden;
    -ms-interpolation-mode: bicubic;
    backface-visibility: hidden;
    -webkit-font-smoothing: antialiased;
    will-change: transform;
}

.product-card:hover .related-image-container img {
    transform: scale(1.05) translateZ(0);
}

.product-link {
    text-decoration: none;
    display: block;
}

.product-title {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    min-height: 3rem;
}

/* ========================================
   TARJETAS Y EFECTOS
======================================== */
.product-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
}

.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* ========================================
   BOTONES
======================================== */
.btn-dark {
    background-color: #1a1a1a;
    border-color: #1a1a1a;
    transition: all 0.3s ease;
}

.btn-dark:hover {
    background-color: #000;
    border-color: #000;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-outline-dark {
    color: #1a1a1a;
    border-color: #1a1a1a;
    transition: all 0.3s ease;
}

.btn-outline-dark:hover {
    background-color: #1a1a1a;
    border-color: #1a1a1a;
    color: white;
    transform: translateY(-2px);
}

/* ========================================
   BREADCRUMB
======================================== */
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 1.5rem;
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #1a1a1a;
}

.breadcrumb-item.active {
    color: #1a1a1a;
    font-weight: 500;
}

/* ========================================
   BADGES
======================================== */
.badge {
    font-weight: 500;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

/* ========================================
   RESPONSIVE
======================================== */
@media (max-width: 991px) {
    .product-image-container {
        height: 400px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
}

@media (max-width: 768px) {
    .product-image-container {
        height: 350px;
        padding: 1.5rem;
    }
    
    .related-image-container {
        height: 180px;
    }
}

@media (max-width: 576px) {
    .product-image-container {
        height: 280px;
        padding: 1rem;
    }
    
    .related-image-container {
        height: 160px;
        padding: 0.75rem;
    }
}
</style>
@endpush
@endsection