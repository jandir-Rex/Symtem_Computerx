<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container position-relative px-4">

        <!-- LOGO IZQUIERDA -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <img src="{{ asset('images/logox.png') }}" alt="Company Computer" height="50">
        </a>

        <!-- BOTÓN HAMBURGUESA (MÓVIL) -->
        <button class="navbar-toggler border-0 position-absolute top-50 end-0 translate-middle-y me-3" 
                type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebareOffcanvas">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- BARRA DE BÚSQUEDA CENTRADA (ESCRITORIO) -->
        <div class="d-none d-lg-flex flex-grow-1 justify-content-center px-5">
            <form action="{{ route('search.global') }}" class="w-100" style="max-width: 600px;" method="GET">
                <div class="input-group input-group-lg shadow-sm position-relative">
                    <input type="search" name="q" class="form-control border-0 rounded-pill-start" 
                            placeholder="Buscar en toda la tienda..." 
                            value="{{ request('q') }}" autocomplete="off"
                            style="height: 50px; font-size: 1rem;">
                    <button class="btn btn-primary rounded-pill-end" type="submit"
                            style="width: 60px;">
                        <i class="fas fa-search fs-5"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- DERECHA: CARRITO + USUARIO -->
        <div class="d-flex align-items-center gap-3">
            @auth   
                <!-- USUARIO LOGUEADO -->
                <div class="dropdown">
                    <a class="text-white dropdown-toggle d-flex align-items-center gap-2 text-decoration-none" 
                        href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fs-2"></i>
                        <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="{{ route('orders.index') }}">Mis Pedidos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item text-danger">Cerrar Sesión</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <!-- NO LOGUEADO: solo el botón de login -->
                <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-4">
                    Iniciar sesión
                </a>
            @endauth

            <!-- CARRITO CON CONTADOR PERSISTENTE -->
            <a href="{{ route('cart.index') }}" class="position-relative text-white text-decoration-none cart-link">
                <i class="fas fa-shopping-cart fs-3"></i>
                @php
                    $cartCount = is_array(session('cart')) ? count(session('cart')) : 0;
                @endphp
                <span id="cart-count" 
                      class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                      style="font-size: 0.7rem;">
                    {{ $cartCount }}
                </span>
            </a>
        </div>
    </div>
</nav>

<!-- BÚSQUEDA MÓVIL (ABAJO DEL NAVBAR) -->
<div class="d-lg-none bg-dark border-top border-secondary py-2 px-3">
    <form action="{{ route('search.global') }}" method="GET">
        <div class="input-group position-relative">
            <input type="search" name="q" class="form-control rounded-pill" 
                    placeholder="Buscar productos..." value="{{ request('q') }}">
            <button class="btn btn-primary rounded-circle" type="submit" 
                    style="width: 40px; height: 40px;">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>
</div>

<!-- OFFCANVAS SIDEBAR -->
<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebareOffcanvas" style="width: 300px;">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title">
            CATEGORÍAS
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        @include('partials.sidebare')
    </div>
</div>

<!-- ESTILOS PRO -->
<style>
    .input-group-lg .form-control {
        padding-left: 1.5rem;
    }
    .input-group-lg button {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .rounded-pill-start {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .rounded-pill-end {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }
    .navbar-brand img {
        transition: transform 0.3s ease;
    }
    .navbar-brand:hover img {
        transform: scale(1.08);
    }
    
    /* Animación sutil del contador */
    #cart-count {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { 
            transform: translate(-50%, -50%) scale(1); 
        }
        50% { 
            transform: translate(-50%, -50%) scale(1.15); 
        }
    }
    
    /* Detener animación al hacer hover */
    .cart-link:hover #cart-count {
        animation: none;
        transform: translate(-50%, -50%) scale(1.2);
        transition: transform 0.2s;
    }
</style>