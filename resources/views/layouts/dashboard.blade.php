<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistema de Gestión</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed: 80px;
            --primary-color: #4f46e5;
            --secondary-color: #64748b;
            --dark-bg: #1e293b;
            --light-bg: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--light-bg);
            overflow-x: hidden;
        }

        /* SIDEBAR */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        #sidebar .logo {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        #sidebar .logo h4 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            transition: opacity 0.3s;
        }

        #sidebar.collapsed .logo h4 {
            opacity: 0;
        }

        .user-info {
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            flex-shrink: 0;
        }

        .user-details {
            color: #fff;
            flex: 1;
        }

        .user-details small {
            color: rgba(255,255,255,0.6);
            display: block;
        }

        #sidebar.collapsed .user-details {
            display: none;
        }

        #sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            text-decoration: none;
        }

        #sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        #sidebar .nav-link.active {
            background: rgba(79, 70, 229, 0.2);
            color: #fff;
            border-left-color: var(--primary-color);
        }

        #sidebar .nav-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        #sidebar.collapsed .nav-link span {
            display: none;
        }

        #sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 1rem;
        }

        /* MAIN CONTENT */
        #content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        #content.expanded {
            margin-left: var(--sidebar-collapsed);
        }

        /* TOPBAR */
        .topbar {
            background: #fff;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar .toggle-btn {
            background: var(--primary-color);
            color: #fff;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .topbar .toggle-btn:hover {
            transform: scale(1.05);
        }

        .topbar .search-box {
            flex: 1;
            max-width: 400px;
            margin: 0 2rem;
        }

        .topbar .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* CARDS */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stat-card p {
            color: var(--secondary-color);
            margin: 0;
        }

        .chart-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
        }

        .chart-card h5 {
            margin-bottom: 1.5rem;
            font-weight: 600;
            color: #1e293b;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            #sidebar.show {
                margin-left: 0;
            }

            #content {
                margin-left: 0;
            }

            .topbar .search-box {
                display: none;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar personalizado para sidebar */
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.05);
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>

    @stack('styles')

    {{-- Prevenir cache entre pestañas --}}
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>
<body>
    {{-- SIDEBAR --}}
    <nav id="sidebar">
        <div class="logo">
            <h4><i class="fas fa-store"></i> <span>COMPANY COMPUTER</span></h4>
        </div>
        
        {{-- Información del usuario --}}
        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-details">
                <strong>{{ auth()->user()->name }}</strong>
                <small>{{ auth()->user()->rol_nombre }}</small>
            </div>
        </div>

        <div class="nav flex-column mt-2">
            {{-- MENÚ ADMINISTRADOR --}}
            @role('Administrador')
               <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                   <i class="fas fa-home"></i>
                   <span>Panel Principal</span>
               </a>

              {{-- 🆕 VENTAS E-COMMERCE --}}
              <a href="{{ route('dashboard.ventas-ecommerce.index') }}" class="nav-link {{ request()->routeIs('dashboard.ventas-ecommerce.*') ? 'active' : '' }}">
                  <i class="fas fa-shopping-cart"></i>
                  <span>Ventas E-commerce</span>
              </a>

              {{-- Reporte de Ventas Stands --}}
              <a href="{{ route('dashboard.ventas.index') }}" class="nav-link {{ request()->routeIs('dashboard.ventas.*') ? 'active' : '' }}">
                  <i class="fas fa-chart-bar"></i>
                  <span>Reporte de Ventas</span>
              </a>
    
             {{-- ALMACÉN ADMIN --}}
             <a href="{{ route('dashboard.almacen.index') }}" class="nav-link {{ request()->routeIs('dashboard.almacen.*') ? 'active' : '' }}">
                 <i class="fas fa-warehouse"></i>
                 <span>Almacén</span>
             </a>

             {{-- PROVEEDORES --}}
             <a href="{{ route('dashboard.proveedores.index') }}" class="nav-link {{ request()->routeIs('dashboard.proveedores.*') ? 'active' : '' }}">
                 <i class="fas fa-truck"></i>
                 <span>Proveedores</span>
             </a>
    
             {{-- EGRESOS --}}
             <a href="{{ route('dashboard.egresos.index') }}" class="nav-link {{ request()->routeIs('dashboard.egresos.*') ? 'active' : '' }}">
                 <i class="fas fa-money-bill-wave"></i>
                 <span>Egresos</span>
             </a>

             {{-- 🆕 GESTIÓN DE USUARIOS --}}
             <a href="{{ route('dashboard.usuarios.index') }}" class="nav-link {{ request()->routeIs('dashboard.usuarios.*') ? 'active' : '' }}">
                 <i class="fas fa-users-cog"></i>
                 <span>Usuarios</span>
             </a>
            @endrole

            {{-- MENÚ CONTADOR --}}
            @role('Contador')
                <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard Contable</span>
                </a>
                <a href="{{ route('dashboard.contador.dashboard') }}" class="nav-link {{ request()->routeIs('dashboard.contador.*') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Reportes SUNAT</span>
                </a>
                <a href="{{ route('dashboard.egresos.index') }}" class="nav-link {{ request()->routeIs('dashboard.egresos.*') ? 'active' : '' }}">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Egresos</span>
                </a>
                <a href="{{ route('stands.stand1.ventas.index') }}" class="nav-link">
                    <i class="fas fa-receipt"></i>
                    <span>Ver Ventas</span>
                </a>
            @endrole

            {{-- MENÚ ALMACÉN --}}
            @role('Almacen')
                <a href="{{ route('almacen.index') }}" class="nav-link {{ request()->routeIs('almacen.*') ? 'active' : '' }}">
                    <i class="fas fa-warehouse"></i>
                    <span>Gestión de Productos</span>
                </a>
                <a href="{{ route('almacen.create') }}" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nuevo Producto</span>
                </a>
            @endrole

            {{-- MENÚ STAND 1 --}}
            @role('Stand1')
                <a href="{{ route('stands.stand1.dashboard') }}" class="nav-link {{ request()->routeIs('stands.stand1.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('stands.stand1.ventas.pos') }}" class="nav-link {{ request()->routeIs('*.pos') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i>
                    <span>Punto de Venta</span>
                </a>
                <a href="{{ route('stands.stand1.ventas.index') }}" class="nav-link {{ request()->routeIs('*.ventas.index') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>Mis Ventas</span>
                </a>
                <a href="{{ route('almacen.index') }}" class="nav-link">
                    <i class="fas fa-box"></i>
                    <span>Ver Stock</span>
                </a>
            @endrole

            {{-- MENÚ STAND 2 --}}
            @role('Stand2')
                <a href="{{ route('stands.stand2.dashboard') }}" class="nav-link {{ request()->routeIs('stands.stand2.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('stands.stand2.ventas.pos') }}" class="nav-link {{ request()->routeIs('*.pos') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i>
                    <span>Punto de Venta</span>
                </a>
                <a href="{{ route('stands.stand2.ventas.index') }}" class="nav-link {{ request()->routeIs('*.ventas.index') ? 'active' : '' }}">
                    <i class="fas fa-list"></i>
                    <span>Mis Ventas</span>
                </a>
                <a href="{{ route('stands.stand2.reparaciones.index') }}" class="nav-link {{ request()->routeIs('*reparaciones*') ? 'active' : '' }}">
                    <i class="fas fa-tools"></i>
                    <span>Reparaciones</span>
                </a>
                <a href="{{ route('almacen.index') }}" class="nav-link">
                    <i class="fas fa-box"></i>
                    <span>Ver Stock</span>
                </a>
            @endrole

            {{-- CERRAR SESIÓN (TODOS) --}}
            <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" style="color: rgba(255,255,255,0.7);">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </button>
            </form>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <div id="content">
        {{-- TOPBAR --}}
        <div class="topbar">
            <button class="toggle-btn" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>

            <div class="search-box">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" placeholder="Buscar...">
                </div>
            </div>

            <div class="user-menu">
                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none" data-bs-toggle="dropdown">
                     <i class="fas fa-bell text-muted"></i>
                      {{-- MODIFICACIÓN AQUÍ: Usar la variable del backend --}}
                     <span class="badge bg-danger rounded-pill" style="font-size: 0.6rem; position: absolute; margin-left: -8px; margin-top: -5px;">
                       {{ $pedidosPendientesCount ?? 0 }}
                     </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><small>Stock bajo: Producto XYZ</small></a></li>
                        <li><a class="dropdown-item" href="#"><small>Nueva venta registrada</small></a></li>
                        <li><a class="dropdown-item" href="#"><small>Cuota pendiente de pago</small></a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <div class="user-avatar" style="width: 40px; height: 40px; background: var(--primary-color); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline text-dark">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ALERTAS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- CONTENT --}}
        <div class="p-4">
            @yield('content')
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        AOS.init({
            duration: 800,
            once: true
        });

        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('content').classList.toggle('expanded');
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        if (window.innerWidth < 768) {
            document.getElementById('sidebar').classList.add('collapsed');
            document.getElementById('content').classList.add('expanded');
        }
    </script>

    @stack('scripts')
</body>
</html>