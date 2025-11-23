@extends('layouts.appe')
@section('title', $query ? 'Resultados para: ' . $query . ' | Company Computer' : 'Buscar productos | Company Computer')
@section('content')

<!-- HEADER DE BÚSQUEDA -->
<section class="search-header py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                @if($query)
                    <h1 class="search-title mb-3">
                        Resultados para: <span class="text-primary">"{{ $query }}"</span>
                    </h1>
                    <p class="search-subtitle">
                        {{ count($results) }} {{ count($results) == 1 ? 'producto encontrado' : 'productos encontrados' }}
                    </p>
                @else
                    <h1 class="search-title mb-3">Buscar Productos</h1>
                    <p class="search-subtitle">Escribe algo en la barra de búsqueda para encontrar productos</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- RESULTADOS -->
<section class="search-results py-5">
    <div class="container">
        @if($query === '')
            <!-- SIN BÚSQUEDA -->
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="empty-title">Comienza a buscar</h3>
                <p class="empty-text">Usa la barra de búsqueda para encontrar productos increíbles</p>
                
                <div class="popular-categories mt-5">
                    <h4 class="mb-4">Categorías populares</h4>
                    <div class="row g-3 justify-content-center">
                        @php
                            $popularCategories = [
                                ['name' => 'Laptops', 'icon' => 'fa-laptop', 'url' => 'laptops'],
                                ['name' => 'PC Gaming', 'icon' => 'fa-desktop', 'url' => 'pc-gaming'],
                                ['name' => 'Monitores', 'icon' => 'fa-tv', 'url' => 'monitores'],
                                ['name' => 'Periféricos', 'icon' => 'fa-keyboard', 'url' => 'perifericos'],
                            ];
                        @endphp
                        @foreach($popularCategories as $cat)
                            <div class="col-6 col-md-3">
                                <a href="{{ url('/' . $cat['url']) }}" class="category-quick-link">
                                    <i class="fas {{ $cat['icon'] }} mb-2"></i>
                                    <span>{{ $cat['name'] }}</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @elseif(empty($results))
            <!-- SIN RESULTADOS -->
            <div class="empty-state text-center py-5">
                <div class="empty-icon mb-4">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h3 class="empty-title">No se encontraron productos</h3>
                <p class="empty-text">No encontramos resultados para <strong>"{{ $query }}"</strong></p>
                
                <div class="search-suggestions mt-4">
                    <p class="mb-3"><strong>Sugerencias:</strong></p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check-circle text-success me-2"></i>Verifica la ortografía</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Intenta con términos más generales</li>
                        <li><i class="fas fa-check-circle text-success me-2"></i>Usa palabras clave diferentes</li>
                    </ul>
                </div>
                
                <a href="{{ route('home') }}" class="btn btn-dark btn-lg mt-4">
                    <i class="fas fa-home me-2"></i>Volver al inicio
                </a>
            </div>
        @else
            <!-- GRID DE PRODUCTOS -->
            <div class="products-grid">
                @foreach($results as $product)
                    {{-- DEBUG: Descomentar para ver los datos --}}
                    {{-- @dump($product) --}}
                    
                    <div class="product-card">
                        <a href="{{ url('/' . $product['category'] . '/' . Str::slug($product['name'])) }}" class="product-image-link">
                            @if(!empty($product['image_url']))
                                <img src="{{ $product['image_url'] }}" 
                                     class="product-image" 
                                     alt="{{ $product['name'] }}"
                                     loading="lazy"
                                     onerror="console.error('Error cargando imagen:', this.src)">
                            @else
                                <div class="product-image-placeholder">
                                    <i class="fas fa-image"></i>
                                    <p class="mt-2">Sin imagen</p>
                                    <small class="text-muted">{{ $product['image'] ?? 'No hay ruta' }}</small>
                                </div>
                            @endif
                        </a>

                        <div class="product-info">
                            <span class="product-category">{{ $product['category_label'] }}</span>
                            <h3 class="product-title">{{ $product['name'] }}</h3>
                            <div class="product-price">S/ {{ number_format($product['price'], 2) }}</div>

                            <div class="product-actions">
                                <a href="{{ url('/' . $product['category'] . '/' . Str::slug($product['name'])) }}" 
                                   class="btn btn-outline-dark btn-sm">
                                    Ver Detalles
                                </a>
                                <button class="btn btn-dark btn-sm add-to-cart"
                                        data-name="{{ $product['name'] }}"
                                        data-price="{{ $product['price'] }}"
                                        data-image="{{ $product['image'] }}"
                                        data-category="{{ $product['category'] }}">
                                    Añadir
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<style>
/* ========================================
   VARIABLES
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
   HEADER DE BÚSQUEDA
======================================== */
.search-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
}

.search-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-color);
}

.search-subtitle {
    font-size: 1.1rem;
    color: #6c757d;
}

.search-form-inline {
    max-width: 600px;
    margin: 0 auto;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 20px;
    color: #6c757d;
    font-size: 1.1rem;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 16px 50px 16px 50px;
    font-size: 1rem;
    border: 2px solid #dee2e6;
    border-radius: var(--border-radius);
    transition: var(--transition);
    background: white;
}

.search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.search-clear {
    position: absolute;
    right: 20px;
    color: #6c757d;
    text-decoration: none;
    transition: var(--transition);
}

.search-clear:hover {
    color: var(--accent-color);
    transform: scale(1.1);
}

/* ========================================
   EMPTY STATE
======================================== */
.empty-state {
    max-width: 600px;
    margin: 0 auto;
}

.empty-icon {
    font-size: 5rem;
    color: #dee2e6;
}

.empty-icon i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.empty-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
}

.empty-text {
    font-size: 1.1rem;
    color: #6c757d;
}

.search-suggestions ul {
    max-width: 400px;
    margin: 0 auto;
}

.search-suggestions li {
    padding: 0.5rem 0;
    text-align: left;
    color: #6c757d;
}

/* ========================================
   CATEGORÍAS RÁPIDAS
======================================== */
.popular-categories h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary-color);
}

.category-quick-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem 1rem;
    background: white;
    border: 2px solid #e9ecef;
    border-radius: var(--border-radius);
    text-decoration: none;
    color: var(--text-color);
    transition: var(--transition);
}

.category-quick-link:hover {
    transform: translateY(-5px);
    border-color: var(--primary-color);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    color: var(--primary-color);
}

.category-quick-link i {
    font-size: 2rem;
    transition: var(--transition);
}

.category-quick-link:hover i {
    transform: scale(1.1);
}

.category-quick-link span {
    font-size: 0.95rem;
    font-weight: 600;
}

/* ========================================
   GRID DE PRODUCTOS
======================================== */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.product-card {
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

.product-image-link {
    display: block;
    overflow: hidden;
    background: #f8f9fa;
}

.product-image {
    width: 100%;
    height: 280px;
    object-fit: contain;
    transition: var(--transition);
    background: white;
    padding: 1rem;
}

.product-image-placeholder {
    width: 100%;
    height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #dee2e6;
}

.product-image-placeholder i {
    font-size: 4rem;
}

.product-image-placeholder p {
    font-size: 0.9rem;
    color: #adb5bd;
    margin: 0;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-info {
    padding: 1.5rem;
}

.product-category {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.product-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.4em;
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

/* ========================================
   RESPONSIVE
======================================== */
@media (max-width: 992px) {
    .search-title {
        font-size: 2rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .search-title {
        font-size: 1.5rem;
    }
    
    .search-subtitle {
        font-size: 1rem;
    }
    
    .search-input {
        padding: 14px 45px 14px 45px;
        font-size: 0.95rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .product-image {
        height: 220px;
    }
    
    .empty-icon {
        font-size: 4rem;
    }
}

@media (max-width: 480px) {
    .products-grid {
        grid-template-columns: 1fr;
    }
    
    .product-actions {
        grid-template-columns: 1fr;
    }
    
    .search-input {
        padding: 12px 40px 12px 40px;
    }
}
</style>

@endsection