@extends('layouts.appe')
@section('title', 'Company Computer - Tu tienda gamer #1 en Perú')
@section('body-class', 'home-page')
@section('content')

<!-- HERO BANNER CON PRODUCTOS DESTACADOS -->
<section class="hero-section mb-5">
    <div class="container-fluid px-0">
        <div id="mainBannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @forelse($destacados as $index => $producto)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <div class="hero-slide">
                            <img src="{{ $producto->imagen_url }}" 
                                 class="hero-image" 
                                 alt="{{ $producto->nombre }}">
                            
                            <div class="hero-overlay"></div>
                            
                            <div class="hero-content">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-8 mx-auto text-center">
                                            <span class="badge badge-featured mb-3">
                                                PRODUCTO DESTACADO
                                            </span>
                                            <h1 class="hero-title mb-3">{{ $producto->nombre }}</h1>
                                            @if($producto->marca)
                                                <p class="hero-brand mb-3">{{ $producto->marca }}</p>
                                            @endif
                                            <div class="hero-price mb-4">
                                                S/ {{ number_format($producto->precio_venta, 2) }}
                                            </div>
                                            <div class="hero-actions">
                                                <a href="{{ route('product.show', [$producto->categoria_url, $producto->slug]) }}" 
                                                   class="btn btn-light btn-lg me-3">
                                                    Ver Detalles
                                                </a>
                                                <button class="btn btn-primary btn-lg add-to-cart"
                                                        data-id="{{ $producto->id }}"
                                                        data-name="{{ $producto->nombre }}"
                                                        data-price="{{ $producto->precio_venta }}"
                                                        data-image="{{ $producto->imagen_url }}"
                                                        data-category="{{ $producto->categoria }}"
                                                        data-stock="{{ $producto->stock }}">
                                                    Añadir al Carrito
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    @php
                        $banners = [
                            'images/banner/banner1.png',
                            'images/banner/banner2.png',
                            'images/banner/banner3.png',
                            'images/banner/banner4.png',
                            'images/banner/banner5.png',
                        ];
                    @endphp
                    @foreach($banners as $index => $src)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="hero-slide">
                                <img src="{{ asset($src) }}" 
                                     class="hero-image" 
                                     alt="Banner Company Computer">
                            </div>
                        </div>
                    @endforeach
                @endforelse
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainBannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

            <div class="carousel-indicators">
                @if($destacados->isNotEmpty())
                    @foreach($destacados as $index => $producto)
                        <button type="button" data-bs-target="#mainBannerCarousel" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                @else
                    @for($i = 0; $i < 5; $i++)
                        <button type="button" data-bs-target="#mainBannerCarousel" data-bs-slide-to="{{ $i }}" 
                                class="{{ $i === 0 ? 'active' : '' }}"></button>
                    @endfor
                @endif
            </div>
        </div>
    </div>
</section>

<!-- CATEGORÍAS CON DISEÑO MINIMALISTA -->
<section class="categories-section py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Explora Nuestras Categorías</h2>
            <p class="section-subtitle">Todo lo que necesitas para tu setup gamer</p>
        </div>
        
        <div class="categories-grid">
            @php
                $allCategories = [
                    ['name' => 'Laptops', 'icon' => 'fa-laptop', 'url' => 'laptops'],
                    ['name' => 'PC Gaming', 'icon' => 'fa-desktop', 'url' => 'pc-gaming'],
                    ['name' => 'Componentes', 'icon' => 'fa-microchip', 'url' => 'componentes'],
                    ['name' => 'Periféricos', 'icon' => 'fa-keyboard', 'url' => 'perifericos'],
                    ['name' => 'Monitores', 'icon' => 'fa-tv', 'url' => 'monitores'],
                    ['name' => 'Consolas', 'icon' => 'fa-gamepad', 'url' => 'consolas'],
                    ['name' => 'Accesorios', 'icon' => 'fa-headset', 'url' => 'accesorios'],
                    ['name' => 'Repuestos', 'icon' => 'fa-tools', 'url' => 'repuestos'],
                ];
            @endphp
            
            @foreach($allCategories as $cat)
                <a href="{{ url('/' . $cat['url']) }}" class="category-card">
                    <div class="category-icon-wrapper">
                        <i class="fas {{ $cat['icon'] }}"></i>
                    </div>
                    <h3 class="category-name">{{ $cat['name'] }}</h3>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- PRODUCTOS NUEVOS -->
@if($nuevos->isNotEmpty())
<section class="products-section py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Recién Llegados</h2>
            <p class="section-subtitle">Los últimos productos añadidos a nuestro catálogo</p>
        </div>
        
        <div class="carousel-wrapper position-relative">
            <button class="carousel-nav-btn prev-btn" onclick="scrollCarousel('nuevos', -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="products-carousel" id="carousel-nuevos">
                @foreach($nuevos as $producto)
                    <div class="product-card">
                        @if($producto->destacado)
                            <span class="product-badge badge-featured">Destacado</span>
                        @endif
                        
                        @if($producto->stock <= $producto->stock_minimo)
                            <span class="product-badge badge-limited">Últimas unidades</span>
                        @endif

                        <a href="{{ route('product.show', [$producto->categoria_url, $producto->slug]) }}" class="product-image-link">
                            <img src="{{ $producto->imagen_url }}" 
                                 class="product-image" 
                                 alt="{{ $producto->nombre }}">
                        </a>

                        <div class="product-info">
                            <h3 class="product-title">{{ $producto->nombre }}</h3>
                            
                            @if($producto->marca)
                                <p class="product-brand">{{ $producto->marca }}</p>
                            @endif

                            <div class="product-price">S/ {{ number_format($producto->precio_venta, 2) }}</div>

                            <div class="product-actions">
                                <a href="{{ route('product.show', [$producto->categoria_url, $producto->slug]) }}" 
                                   class="btn btn-outline-dark btn-sm">
                                    Ver Detalles
                                </a>
                                <button class="btn btn-dark btn-sm add-to-cart"
                                        data-id="{{ $producto->id }}"
                                        data-name="{{ $producto->nombre }}"
                                        data-price="{{ $producto->precio_venta }}"
                                        data-image="{{ $producto->imagen_url }}"
                                        data-category="{{ $producto->categoria }}"
                                        data-stock="{{ $producto->stock }}">
                                    Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-nav-btn next-btn" onclick="scrollCarousel('nuevos', 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
@endif

<!-- LAPTOPS GAMER -->
@if($laptops->isNotEmpty())
<section class="products-section py-5">
    <div class="container">
        <div class="section-header-inline mb-4">
            <h2 class="section-title">Laptops</h2>
            <a href="{{ route('category.laptops') }}" class="btn btn-link">
                Ver todas <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="carousel-wrapper position-relative">
            <button class="carousel-nav-btn prev-btn" onclick="scrollCarousel('laptops', -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="products-carousel" id="carousel-laptops">
                @foreach($laptops as $producto)
                    <div class="product-card-compact">
                        <a href="{{ route('product.show', [$producto->categoria_url, $producto->slug]) }}">
                            <img src="{{ $producto->imagen_url }}" 
                                 class="product-image" 
                                 alt="{{ $producto->nombre }}">
                        </a>
                        <div class="product-info">
                            <h3 class="product-title">{{ $producto->nombre }}</h3>
                            <div class="product-price">S/ {{ number_format($producto->precio_venta, 2) }}</div>
                            <button class="btn btn-dark btn-sm w-100 add-to-cart"
                                    data-id="{{ $producto->id }}"
                                    data-name="{{ $producto->nombre }}"
                                    data-price="{{ $producto->precio_venta }}"
                                    data-image="{{ $producto->imagen_url }}"
                                    data-category="{{ $producto->categoria }}"
                                    data-stock="{{ $producto->stock }}">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-nav-btn next-btn" onclick="scrollCarousel('laptops', 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
@endif

<!-- PCS GAMER -->
@if($pcsGamer->isNotEmpty())
<section class="products-section py-5 bg-light">
    <div class="container">
        <div class="section-header-inline mb-4">
            <h2 class="section-title">PC Gaming</h2>
            <a href="{{ route('category.pc-gaming') }}" class="btn btn-link">
                Ver todas <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="carousel-wrapper position-relative">
            <button class="carousel-nav-btn prev-btn" onclick="scrollCarousel('pcs', -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="products-carousel" id="carousel-pcs">
                @foreach($pcsGamer as $producto)
                    <div class="product-card-compact">
                        <a href="{{ route('product.show', [$producto->categoria_url, $producto->slug]) }}">
                            <img src="{{ $producto->imagen_url }}" 
                                 class="product-image" 
                                 alt="{{ $producto->nombre }}">
                        </a>
                        <div class="product-info">
                            <h3 class="product-title">{{ $producto->nombre }}</h3>
                            <div class="product-price">S/ {{ number_format($producto->precio_venta, 2) }}</div>
                            <button class="btn btn-dark btn-sm w-100 add-to-cart"
                                    data-id="{{ $producto->id }}"
                                    data-name="{{ $producto->nombre }}"
                                    data-price="{{ $producto->precio_venta }}"
                                    data-image="{{ $producto->imagen_url }}"
                                    data-category="{{ $producto->categoria }}"
                                    data-stock="{{ $producto->stock }}">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-nav-btn next-btn" onclick="scrollCarousel('pcs', 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
@endif

<!-- MONITORES -->
@if($monitores->isNotEmpty())
<section class="products-section py-5">
    <div class="container">
        <div class="section-header-inline mb-4">
            <h2 class="section-title">Monitores</h2>
            <a href="{{ route('category.monitores') }}" class="btn btn-link">
                Ver todos <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="carousel-wrapper position-relative">
            <button class="carousel-nav-btn prev-btn" onclick="scrollCarousel('monitores', -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="products-carousel" id="carousel-monitores">
                @foreach($monitores as $producto)
                    <div class="product-card-compact">
                        <a href="{{ route('product.show', [$producto->categoria_url, $producto->slug]) }}">
                            <img src="{{ $producto->imagen_url }}" 
                                 class="product-image" 
                                 alt="{{ $producto->nombre }}">
                        </a>
                        <div class="product-info">
                            <h3 class="product-title">{{ $producto->nombre }}</h3>
                            <div class="product-price">S/ {{ number_format($producto->precio_venta, 2) }}</div>
                            <button class="btn btn-dark btn-sm w-100 add-to-cart"
                                    data-id="{{ $producto->id }}"
                                    data-name="{{ $producto->nombre }}"
                                    data-price="{{ $producto->precio_venta }}"
                                    data-image="{{ $producto->imagen_url }}"
                                    data-category="{{ $producto->categoria }}"
                                    data-stock="{{ $producto->stock }}">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-nav-btn next-btn" onclick="scrollCarousel('monitores', 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
@endif

<!-- ACCESORIOS -->
@if($accesorios->isNotEmpty())
<section class="products-section py-5 bg-light">
    <div class="container">
        <div class="section-header-inline mb-4">
            <h2 class="section-title">Accesorios</h2>
            <a href="{{ route('category.accesorios') }}" class="btn btn-link">
                Ver todos <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        
        <div class="carousel-wrapper position-relative">
            <button class="carousel-nav-btn prev-btn" onclick="scrollCarousel('accesorios', -1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="products-carousel" id="carousel-accesorios">
                @foreach($accesorios as $producto)
                    <div class="product-card-compact">
                        <a href="{{ route('product.show', [$producto->categoria_url, $producto->slug]) }}">
                            <img src="{{ $producto->imagen_url }}" 
                                 class="product-image" 
                                 alt="{{ $producto->nombre }}">
                        </a>
                        <div class="product-info">
                            <h3 class="product-title">{{ $producto->nombre }}</h3>
                            <div class="product-price">S/ {{ number_format($producto->precio_venta, 2) }}</div>
                            <button class="btn btn-dark btn-sm w-100 add-to-cart"
                                    data-id="{{ $producto->id }}"
                                    data-name="{{ $producto->nombre }}"
                                    data-price="{{ $producto->precio_venta }}"
                                    data-image="{{ $producto->imagen_url }}"
                                    data-category="{{ $producto->categoria }}"
                                    data-stock="{{ $producto->stock }}">
                                Añadir al Carrito
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button class="carousel-nav-btn next-btn" onclick="scrollCarousel('accesorios', 1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>
@endif

<style>
/* ========================================
   VARIABLES Y CONFIGURACIÓN BASE
======================================== */
:root {
    --primary-color: #1a1a1a;
    --secondary-color: #333333;
    --accent-color: #dc3545;
    --text-color: #2c3e50;
    --light-bg: #f8f9fa;
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ========================================
   HERO BANNER
======================================== */
.hero-section {
    background: #000;
}

.hero-slide {
    position: relative;
    height: 600px;
    overflow: hidden;
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7));
}

.hero-content {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    z-index: 2;
    color: white;
}

.badge-featured {
    display: inline-block;
    background: var(--accent-color);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1rem;
}

.hero-brand {
    font-size: 1.25rem;
    opacity: 0.9;
}

.hero-price {
    font-size: 3rem;
    font-weight: 700;
    color: white;
}

.hero-actions .btn {
    padding: 14px 32px;
    font-weight: 600;
    border-radius: 8px;
    transition: var(--transition);
}

/* ========================================
   SECCIONES
======================================== */
.section-header {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
}

.section-header-inline {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.section-header-inline .section-title {
    font-size: 1.75rem;
    margin: 0;
}

/* ========================================
   CATEGORÍAS
======================================== */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1.5rem;
}

.category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 2rem 1rem;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius);
    text-decoration: none;
    color: var(--text-color);
    transition: var(--transition);
}

.category-card:hover {
    transform: translateY(-5px);
    border-color: var(--primary-color);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.category-icon-wrapper {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--light-bg);
    border-radius: 50%;
    margin-bottom: 1rem;
    transition: var(--transition);
}

.category-card:hover .category-icon-wrapper {
    background: var(--primary-color);
    color: white;
}

.category-icon-wrapper i {
    font-size: 1.75rem;
}

.category-name {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0;
}

/* ========================================
   CARRUSEL DE PRODUCTOS
======================================== */
.carousel-wrapper {
    position: relative;
    padding: 0 50px;
}

.products-carousel {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    scroll-behavior: smooth;
    padding: 1rem 0;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.products-carousel::-webkit-scrollbar {
    display: none;
}

.carousel-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: white;
    border: 2px solid var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: var(--transition);
    color: var(--primary-color);
}

.carousel-nav-btn:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-50%) scale(1.1);
}

.carousel-nav-btn.prev-btn {
    left: 0;
}

.carousel-nav-btn.next-btn {
    right: 0;
}

.carousel-nav-btn i {
    font-size: 1rem;
}

.product-card {
    flex: 0 0 280px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    position: relative;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.product-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 2;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-featured {
    background: var(--accent-color);
    color: white;
}

.badge-limited {
    background: #ffc107;
    color: #000;
}

.product-image-link {
    display: block;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 280px;
    object-fit: cover;
    transition: var(--transition);
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-info {
    padding: 1.5rem;
}

.product-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-brand {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 0.75rem;
}

.product-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.product-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

/* ========================================
   PRODUCTOS - GRID COMPACTO
======================================== */
.product-card-compact {
    flex: 0 0 280px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
}

.product-card-compact:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.product-card-compact .product-image {
    height: 200px;
}

.product-card-compact .product-info {
    padding: 1rem;
}

.product-card-compact .product-title {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.product-card-compact .product-price {
    font-size: 1.25rem;
    margin-bottom: 0.75rem;
}

/* ========================================
   BOTONES
======================================== */
.btn {
    font-weight: 600;
    border-radius: 8px;
    transition: var(--transition);
}

.btn-dark {
    background: var(--primary-color);
    border: none;
}

.btn-dark:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

.btn-outline-dark {
    border: 2px solid var(--primary-color);
    color: var(--primary-color);
}

.btn-outline-dark:hover {
    background: var(--primary-color);
    color: white;
}

.btn-link {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 600;
}

.btn-link:hover {
    color: var(--accent-color);
}

/* ========================================
   RESPONSIVE
======================================== */
@media (max-width: 992px) {
    .hero-slide {
        height: 500px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-price {
        font-size: 2.5rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .hero-slide {
        height: 400px;
    }
    
    .hero-title {
        font-size: 1.75rem;
    }
    
    .hero-price {
        font-size: 2rem;
    }
    
    .hero-actions .btn {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
    
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 1rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
    
    .product-card {
        flex: 0 0 240px;
    }
    
    .product-card-compact {
        flex: 0 0 240px;
    }
    
    .carousel-wrapper {
        padding: 0 40px;
    }
    
    .carousel-nav-btn {
        width: 35px;
        height: 35px;
    }
}

@media (max-width: 480px) {
    .hero-slide {
        height: 350px;
    }
    
    .hero-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .hero-actions .btn {
        width: 100%;
        margin: 0 !important;
    }
    
    .categories-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .carousel-wrapper {
        padding: 0 35px;
    }
    
    .carousel-nav-btn {
        width: 30px;
        height: 30px;
    }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar carrusel principal
    const carousel = document.querySelector('#mainBannerCarousel');
    if (carousel) {
        new bootstrap.Carousel(carousel, {
            interval: 5000,
            wrap: true,
            pause: 'hover'
        });
    }
});

// Función mejorada para controlar los carruseles de productos
function scrollCarousel(carouselId, direction) {
    const carousel = document.getElementById('carousel-' + carouselId);
    if (!carousel) {
        console.log('Carrusel no encontrado:', carouselId);
        return;
    }
    
    const cardWidth = 300; // Ancho de cada card + gap
    const scrollAmount = cardWidth * direction;
    
    carousel.scrollBy({
        left: scrollAmount,
        behavior: 'smooth'
    });
}

// Alternativa: Agregar event listeners directamente
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los botones de navegación
    document.querySelectorAll('.carousel-nav-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const carouselWrapper = this.closest('.carousel-wrapper');
            const carousel = carouselWrapper.querySelector('.products-carousel');
            const direction = this.classList.contains('prev-btn') ? -1 : 1;
            const cardWidth = 300;
            
            carousel.scrollBy({
                left: cardWidth * direction,
                behavior: 'smooth'
            });
        });
    });
});
</script>
@endpush
@endsection