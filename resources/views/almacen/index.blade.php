@extends('layouts.app')

@section('title', 'Almacén de Productos')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">
            <i class="fas fa-warehouse text-primary"></i> Almacén de Productos
        </h3>
        <div>
            {{-- BOTÓN DE IMPORTAR EXCEL --}}
            <button type="button" class="btn btn-success shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#modalImportar">
                <i class="fas fa-file-excel"></i> Importar Excel
            </button>
            <a href="{{ route('almacen.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </a>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {!! session('warning') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            
            @if(session('errores_importacion'))
                <hr>
                <strong>Errores encontrados:</strong>
                <ul class="mb-0 mt-2">
                    @foreach(session('errores_importacion') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- INDICADORES RÁPIDOS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-barcode fa-2x text-primary mb-2"></i>
                    <p class="mt-2 mb-1 text-muted small">Total SKUs</p>
                    <h4 class="fw-bold mb-0">{{ $stats['total_skus'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-cubes fa-2x text-success mb-2"></i>
                    <p class="mt-2 mb-1 text-muted small">Unidades Totales</p>
                    <h4 class="fw-bold mb-0">{{ number_format($stats['total_unidades']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning mb-2"></i>
                    <p class="mt-2 mb-1 text-muted small">Stock Bajo</p>
                    <h4 class="fw-bold mb-0">{{ $stats['alerta_stock_bajo'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-shopping-cart fa-2x text-info mb-2"></i>
                    <p class="mt-2 mb-1 text-muted small">En E-commerce</p>
                    <h4 class="fw-bold mb-0">
                        <span id="ecommerce-count">{{ \App\Models\Producto::where('visible_ecommerce', true)->count() }}</span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- BUSCADOR Y FILTROS EN TIEMPO REAL ⚡ --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('almacen.index') }}" id="formFiltros">
                <div class="row g-3 align-items-end">
                    {{-- BUSCADOR --}}
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1 fw-semibold">
                            <i class="fas fa-search me-1"></i>Buscar producto
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   name="buscar" 
                                   class="form-control border-start-0" 
                                   placeholder="Nombre o código de barras..." 
                                   value="{{ request('buscar') }}"
                                   autocomplete="off">
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle fa-xs me-1"></i>Se filtra automáticamente al escribir
                        </small>
                    </div>

                    {{-- CATEGORÍA --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1 fw-semibold">
                            <i class="fas fa-folder me-1"></i>Categoría
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-folder text-muted"></i>
                            </span>
                            <select name="categoria" class="form-select border-start-0">
                                <option value="">Todas</option>
                                @foreach($categorias as $key => $value)
                                    <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- E-COMMERCE --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1 fw-semibold">
                            <i class="fas fa-store me-1"></i>E-commerce
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-store text-muted"></i>
                            </span>
                            <select name="visible_ecommerce" class="form-select border-start-0">
                                <option value="">Todos</option>
                                <option value="1" {{ request('visible_ecommerce') == '1' ? 'selected' : '' }}>
                                    ✅ Visible
                                </option>
                                <option value="0" {{ request('visible_ecommerce') == '0' ? 'selected' : '' }}>
                                    ❌ Oculto
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1 fw-semibold">
                            <i class="fas fa-toggle-on me-1"></i>Estado
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-toggle-on text-muted"></i>
                            </span>
                            <select name="activo" class="form-select border-start-0">
                                <option value="">Todos</option>
                                <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                                <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                    </div>

                    {{-- BOTÓN LIMPIAR FILTROS 🧹 --}}
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger w-100 btn-limpiar-filtros" title="Limpiar todos los filtros">
                            <i class="fas fa-broom me-2"></i> Limpiar Filtros
                        </button>
                    </div>
                </div>

                {{-- BADGE DE FILTROS ACTIVOS --}}
                @if(request()->hasAny(['buscar', 'categoria', 'visible_ecommerce', 'activo']))
                <div class="mt-3">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="badge bg-primary">
                            <i class="fas fa-filter me-1"></i> Filtros activos:
                        </span>
                        
                        @if(request('buscar'))
                            <span class="badge bg-info">
                                <i class="fas fa-search me-1"></i>Búsqueda: "{{ request('buscar') }}"
                            </span>
                        @endif
                        
                        @if(request('categoria'))
                            <span class="badge bg-secondary">
                                <i class="fas fa-folder me-1"></i>{{ $categorias[request('categoria')] ?? request('categoria') }}
                            </span>
                        @endif
                        
                        @if(request('visible_ecommerce') !== null)
                            <span class="badge {{ request('visible_ecommerce') == '1' ? 'bg-success' : 'bg-dark' }}">
                                <i class="fas fa-store me-1"></i>E-commerce: {{ request('visible_ecommerce') == '1' ? 'Visible' : 'Oculto' }}
                            </span>
                        @endif
                        
                        @if(request('activo') !== null)
                            <span class="badge {{ request('activo') == '1' ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas fa-power-off me-1"></i>{{ request('activo') == '1' ? 'Activos' : 'Inactivos' }}
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- TABLA DE PRODUCTOS --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80" class="text-center">
                                <i class="fas fa-image text-muted"></i>
                            </th>
                            <th>
                                <i class="fas fa-tag text-muted me-1"></i> Producto
                            </th>
                            <th>
                                <i class="fas fa-folder text-muted me-1"></i> Categoría
                            </th>
                            <th class="text-center">
                                <i class="fas fa-boxes text-muted me-1"></i> Stock
                            </th>
                            <th>
                                <i class="fas fa-dollar-sign text-muted me-1"></i> Precio
                            </th>
                            <th class="text-center">
                                <i class="fas fa-power-off text-muted me-1"></i> Estado
                            </th>
                            <th class="text-center">
                                <i class="fas fa-shopping-cart text-muted me-1"></i> E-commerce
                            </th>
                            <th class="text-end">
                                <i class="fas fa-cog text-muted me-1"></i> Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr data-producto-id="{{ $producto->id }}" 
                            @if($producto->enStockMinimo()) class="table-warning" @endif>
                            {{-- IMAGEN --}}
                            <td class="text-center">
                                <img src="{{ $producto->imagen_url }}" 
                                     width="50" height="50" 
                                     class="rounded border" 
                                     style="object-fit: cover;"
                                     alt="{{ $producto->nombre }}">
                            </td>

                            {{-- NOMBRE --}}
                            <td>
                                <div class="fw-semibold">{{ $producto->nombre }}</div>
                                @if($producto->marca)
                                    <small class="text-muted">
                                        <i class="fas fa-copyright fa-xs me-1"></i>{{ $producto->marca }}
                                    </small>
                                @endif
                            </td>

                            {{-- CATEGORÍA --}}
                            <td>
                                <span class="badge bg-secondary">
                                    <i class="fas fa-folder-open fa-xs me-1"></i>
                                    {{ $categorias[$producto->categoria] ?? ucfirst($producto->categoria) }}
                                </span>
                            </td>

                            {{-- STOCK --}}
                            <td class="text-center stock-value">
                                @if($producto->stock <= 0)
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle fa-xs me-1"></i>Sin Stock
                                    </span>
                                @elseif($producto->stock <= $producto->stock_minimo)
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle fa-xs me-1"></i>{{ $producto->stock }}
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle fa-xs me-1"></i>{{ $producto->stock }}
                                    </span>
                                @endif
                            </td>

                            {{-- PRECIO --}}
                            <td>
                                <strong>
                                    <i class="fas fa-coins text-warning fa-xs me-1"></i>
                                    S/ {{ number_format($producto->precio_venta, 2) }}
                                </strong>
                            </td>

                            {{-- ESTADO ACTIVO --}}
                            <td class="text-center">
                                @if($producto->activo)
                                    <i class="fas fa-check-circle text-success fs-5" title="Activo"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger fs-5" title="Inactivo"></i>
                                @endif
                            </td>

                            {{-- TOGGLE E-COMMERCE --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input toggle-ecommerce" 
                                               type="checkbox" 
                                               role="switch"
                                               id="ecommerce-{{ $producto->id }}"
                                               data-producto-id="{{ $producto->id }}"
                                               {{ $producto->visible_ecommerce ? 'checked' : '' }}
                                               {{ !$producto->activo || $producto->stock <= 0 ? 'disabled' : '' }}
                                               style="cursor: pointer; transform: scale(1.3);">
                                    </div>
                                    <span id="badge-ecommerce-{{ $producto->id }}" 
                                          class="badge ms-2 {{ $producto->visible_ecommerce ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas {{ $producto->visible_ecommerce ? 'fa-eye' : 'fa-eye-slash' }} fa-xs"></i>
                                        {{ $producto->visible_ecommerce ? 'Visible' : 'Oculto' }}
                                    </span>
                                </div>
                                @if(!$producto->activo || $producto->stock <= 0)
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle fa-xs me-1"></i>
                                        {{ !$producto->activo ? 'Inactivo' : 'Sin stock' }}
                                    </small>
                                @endif
                            </td>

                            {{-- ACCIONES --}}
                            <td class="text-end">
                                <a href="{{ route('almacen.edit', $producto) }}" 
                                   class="btn btn-sm btn-outline-primary"
                                   title="Editar producto">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                                <h5>No hay productos registrados</h5>
                                <p class="small">Comienza agregando tu primer producto</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOTER CON PAGINACIÓN --}}
        @if($productos->hasPages())
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Mostrando {{ $productos->firstItem() }} - {{ $productos->lastItem() }} de {{ $productos->total() }} productos
                </div>
                <div>
                    {{ $productos->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- PRODUCTOS CRÍTICOS (STOCK BAJO) --}}
    @if($productos_criticos->isNotEmpty())
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-warning bg-opacity-10 border-0">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Productos con Stock Crítico
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($productos_criticos as $critico)
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-2 bg-light rounded border">
                        <img src="{{ $critico->imagen_url }}" 
                             class="rounded me-3" 
                             style="width: 45px; height: 45px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ $critico->nombre }}</div>
                            <small class="text-muted">
                                <i class="fas fa-box fa-xs me-1"></i>Stock: {{ $critico->stock }} / 
                                <i class="fas fa-chart-line fa-xs me-1"></i>Mínimo: {{ $critico->stock_minimo }}
                            </small>
                        </div>
                        <span class="badge bg-warning">
                            <i class="fas fa-bell fa-xs me-1"></i>¡Reponer!
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

{{-- MODAL IMPORTAR EXCEL --}}
<div class="modal fade" id="modalImportar" tabindex="-1" aria-labelledby="modalImportarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalImportarLabel">
                    <i class="fas fa-file-excel me-2"></i> Importar Productos desde Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('almacen.importar') }}" method="POST" enctype="multipart/form-data" id="formImportar">
                @csrf
                <div class="modal-body">
                    {{-- PASO 1: Descargar plantilla --}}
                    <div class="alert alert-primary border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-download fa-3x me-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1">
                                    <i class="fas fa-step-forward me-1"></i>PASO 1: Descarga la plantilla oficial
                                </h6>
                                <p class="mb-0 small">Usa SOLO esta plantilla para garantizar compatibilidad</p>
                            </div>
                            <a href="{{ route('almacen.plantilla') }}" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i> Descargar
                            </a>
                        </div>
                    </div>

                    {{-- PASO 2: Completar datos --}}
                    <div class="alert alert-info border-0 shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-edit fa-2x me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-step-forward me-1"></i>PASO 2: Completa los datos
                                </h6>
                                <ul class="mb-0 small">
                                    <li><i class="fas fa-check text-success me-1"></i><strong>Columnas obligatorias:</strong> nombre, precio_compra, precio_venta, stock</li>
                                    <li><i class="fas fa-ban text-danger me-1"></i><strong>NO modifiques</strong> los nombres de las columnas</li>
                                    <li><i class="fas fa-trash text-danger me-1"></i><strong>Elimina las filas de ejemplo</strong> antes de agregar tus productos</li>
                                    <li><i class="fas fa-calculator text-primary me-1"></i>Precios <strong>SIN IGV</strong> (se calculará automáticamente)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- PASO 3: Subir archivo --}}
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-cloud-upload-alt fa-2x me-3 mt-1"></i>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-step-forward me-1"></i>PASO 3: Sube tu archivo
                                </h6>
                                <input 
                                    type="file" 
                                    name="archivo_excel" 
                                    id="archivo_excel" 
                                    class="form-control" 
                                    accept=".xlsx,.xls" 
                                    required
                                >
                                <small class="text-muted">
                                    <i class="fas fa-file-excel me-1"></i>Solo archivos .xlsx o .xls (máx. 10MB)
                                </small>
                                
                                <div id="archivoSeleccionado" class="mt-2 alert alert-success py-2 px-3" style="display:none;">
                                    <i class="fas fa-check-circle me-2"></i> 
                                    <strong>Archivo listo:</strong> <span id="nombreArchivo"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Advertencias --}}
                    <div class="alert alert-warning border-0 mb-0">
                        <h6 class="fw-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i>Importante:
                        </h6>
                        <ul class="mb-0 small">
                            <li><i class="fas fa-percent text-success me-1"></i>El IGV (18%) se agrega automáticamente al precio de venta</li>
                            <li><i class="fas fa-sync text-primary me-1"></i>Si un producto ya existe, se actualizará</li>
                            <li><i class="fas fa-exclamation-circle text-danger me-1"></i>Las filas con errores se omitirán con reporte</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="btnImportar" disabled>
                        <i class="fas fa-upload me-1"></i> Importar Productos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // FILTROS EN TIEMPO REAL ⚡
    // ========================================
    const formFiltros = document.getElementById('formFiltros');
    const inputBuscar = document.querySelector('input[name="buscar"]');
    const selectCategoria = document.querySelector('select[name="categoria"]');
    const selectEcommerce = document.querySelector('select[name="visible_ecommerce"]');
    const selectActivo = document.querySelector('select[name="activo"]');
    const btnLimpiar = document.querySelector('.btn-limpiar-filtros');
    
    let timeoutId = null;

    function aplicarFiltros() {
        if (formFiltros) {
            mostrarCargando();
            formFiltros.submit();
        }
    }

    if (inputBuscar) {
        inputBuscar.addEventListener('input', function() {
            clearTimeout(timeoutId);
            this.classList.add('border-primary', 'shadow-sm');
            
            timeoutId = setTimeout(() => {
                this.classList.remove('border-primary', 'shadow-sm');
                aplicarFiltros();
            }, 600);
        });
    }

    if (selectCategoria) {
        selectCategoria.addEventListener('change', function() {
            this.classList.add('border-primary', 'shadow-sm');
            setTimeout(() => {
                this.classList.remove('border-primary', 'shadow-sm');
            }, 300);
            aplicarFiltros();
        });
    }

    if (selectEcommerce) {
        selectEcommerce.addEventListener('change', function() {
            this.classList.add('border-primary', 'shadow-sm');
            setTimeout(() => {
                this.classList.remove('border-primary', 'shadow-sm');
            }, 300);
            aplicarFiltros();
        });
    }

    if (selectActivo) {
        selectActivo.addEventListener('change', function() {
            this.classList.add('border-primary', 'shadow-sm');
            setTimeout(() => {
                this.classList.remove('border-primary', 'shadow-sm');
            }, 300);
            aplicarFiltros();
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function(e) {
            e.preventDefault();
            
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Limpiando...';
            this.disabled = true;
            
            if (inputBuscar) inputBuscar.value = '';
            if (selectCategoria) selectCategoria.value = '';
            if (selectEcommerce) selectEcommerce.value = '';
            if (selectActivo) selectActivo.value = '';
            
            setTimeout(() => {
                window.location.href = formFiltros.action;
            }, 300);
        });
    }

    function mostrarCargando() {
        const indicador = document.createElement('div');
        indicador.id = 'loading-overlay';
        indicador.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
        indicador.style.cssText = 'background: rgba(255,255,255,0.85); z-index: 9999; backdrop-filter: blur(3px);';
        indicador.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="fw-bold text-primary mb-0">
                    <i class="fas fa-filter me-2"></i>Filtrando productos...
                </p>
            </div>
        `;
        document.body.appendChild(indicador);
    }

    // ========================================
    // TOGGLE E-COMMERCE ✅ UNIFICADO
    // ========================================
    const ecommerceToggles = document.querySelectorAll('.toggle-ecommerce');
    
    ecommerceToggles.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const productoId = this.dataset.productoId;
            const isChecked = this.checked;
            const originalState = !isChecked;
            
            // Obtener token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            
            if (!csrfToken) {
                console.error('❌ CSRF token no encontrado');
                showNotification('Error: Token de seguridad no encontrado', 'danger');
                this.checked = originalState;
                return;
            }
            
            const token = csrfToken.getAttribute('content');
            
            // 🔥 RUTA UNIFICADA - Funciona para admin y almacén
            const url = `/almacen/${productoId}/toggle-ecommerce`;
            
            console.log('🔄 Toggle E-commerce:', {
                url: url,
                productoId: productoId,
                nuevoEstado: isChecked ? 'Visible' : 'Oculto',
                vista: window.location.pathname.includes('/admin/') ? 'ADMIN' : 'ALMACÉN'
            });
            
            // Deshabilitar mientras procesa
            this.disabled = true;
            const badge = document.querySelector(`#badge-ecommerce-${productoId}`);
            
            // Cambiar badge temporalmente
            if (badge) {
                badge.innerHTML = '<i class="fas fa-spinner fa-spin fa-xs"></i> Procesando...';
                badge.className = 'badge ms-2 bg-warning';
            }
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    visible_ecommerce: isChecked ? 1 : 0
                })
            })
            .then(response => {
                console.log('📡 Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('✅ Response data:', data);
                
                if (data.success) {
                    // Actualizar badge visual
                    if (badge) {
                        if (isChecked) {
                            badge.classList.remove('bg-secondary', 'bg-warning');
                            badge.classList.add('bg-success');
                            badge.innerHTML = '<i class="fas fa-eye fa-xs"></i> Visible';
                        } else {
                            badge.classList.remove('bg-success', 'bg-warning');
                            badge.classList.add('bg-secondary');
                            badge.innerHTML = '<i class="fas fa-eye-slash fa-xs"></i> Oculto';
                        }
                    }
                    
                    // Actualizar contador (si existe en la página)
                    const counterElement = document.querySelector('#ecommerce-count');
                    if (counterElement) {
                        const currentCount = parseInt(counterElement.textContent) || 0;
                        const newCount = currentCount + (isChecked ? 1 : -1);
                        counterElement.textContent = newCount;
                        
                        // Animación del contador
                        counterElement.classList.add('text-primary', 'fw-bold');
                        setTimeout(() => {
                            counterElement.classList.remove('text-primary', 'fw-bold');
                        }, 1000);
                        
                        console.log('📊 Contador actualizado:', {
                            anterior: currentCount,
                            nuevo: newCount
                        });
                    }
                    
                    // Notificación de éxito
                    showNotification(data.message, 'success');
                    
                    // Efecto visual en la fila completa
                    const row = this.closest('tr');
                    if (row) {
                        row.classList.add('table-success');
                        setTimeout(() => {
                            row.classList.remove('table-success');
                        }, 1500);
                    }
                    
                } else {
                    // Revertir el checkbox
                    this.checked = originalState;
                    
                    // Restaurar badge
                    if (badge) {
                        if (originalState) {
                            badge.classList.remove('bg-secondary', 'bg-warning');
                            badge.classList.add('bg-success');
                            badge.innerHTML = '<i class="fas fa-eye fa-xs"></i> Visible';
                        } else {
                            badge.classList.remove('bg-success', 'bg-warning');
                            badge.classList.add('bg-secondary');
                            badge.innerHTML = '<i class="fas fa-eye-slash fa-xs"></i> Oculto';
                        }
                    }
                    
                    showNotification(data.message || 'Error al cambiar visibilidad', 'danger');
                }
            })
            .catch(error => {
                console.error('❌ Error completo:', error);
                
                // Revertir el checkbox
                this.checked = originalState;
                
                // Restaurar badge
                if (badge) {
                    if (originalState) {
                        badge.classList.remove('bg-secondary', 'bg-warning');
                        badge.classList.add('bg-success');
                        badge.innerHTML = '<i class="fas fa-eye fa-xs"></i> Visible';
                    } else {
                        badge.classList.remove('bg-success', 'bg-warning');
                        badge.classList.add('bg-secondary');
                        badge.innerHTML = '<i class="fas fa-eye-slash fa-xs"></i> Oculto';
                    }
                }
                
                showNotification('Error de conexión. Intenta nuevamente', 'danger');
            })
            .finally(() => {
                // Rehabilitar el checkbox
                this.disabled = false;
            });
        });
    });

    // ========================================
    // MODAL IMPORTAR
    // ========================================
    const inputArchivo = document.getElementById('archivo_excel');
    const archivoSeleccionado = document.getElementById('archivoSeleccionado');
    const nombreArchivo = document.getElementById('nombreArchivo');
    const formImportar = document.getElementById('formImportar');
    const btnImportar = document.getElementById('btnImportar');
    const modalElement = document.getElementById('modalImportar');

    if (inputArchivo) {
        inputArchivo.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const archivo = this.files[0];
                const tamaño = (archivo.size / 1024).toFixed(2);
                nombreArchivo.textContent = `${archivo.name} (${tamaño} KB)`;
                archivoSeleccionado.style.display = 'block';
                btnImportar.disabled = false;
            } else {
                archivoSeleccionado.style.display = 'none';
                btnImportar.disabled = true;
            }
        });
    }

    if (formImportar) {
        formImportar.addEventListener('submit', function(e) {
            const archivo = inputArchivo.files[0];
            
            if (!archivo) {
                e.preventDefault();
                alert('⚠️ Por favor selecciona un archivo');
                return false;
            }

            if (archivo.size > 10 * 1024 * 1024) {
                e.preventDefault();
                alert('⚠️ El archivo es muy grande. Máximo 10MB permitido');
                return false;
            }

            const extension = archivo.name.split('.').pop().toLowerCase();
            if (!['xlsx', 'xls'].includes(extension)) {
                e.preventDefault();
                alert('⚠️ Solo se aceptan archivos .xlsx o .xls');
                return false;
            }

            btnImportar.disabled = true;
            btnImportar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importando...';
        });
    }

    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function() {
            if (formImportar) formImportar.reset();
            if (archivoSeleccionado) archivoSeleccionado.style.display = 'none';
            if (btnImportar) {
                btnImportar.disabled = true;
                btnImportar.innerHTML = '<i class="fas fa-upload me-1"></i> Importar Productos';
            }
        });
    }

    // Auto-cerrar alertas después de 8 segundos
    const alertas = document.querySelectorAll('.alert-dismissible');
    alertas.forEach(function(alerta) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alerta);
            bsAlert.close();
        }, 8000);
    });
});

// ========================================
// FUNCIÓN PARA MOSTRAR NOTIFICACIONES
// ========================================
function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    alert.innerHTML = `
        <i class="fas ${icon} me-2"></i>
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto-cerrar después de 4 segundos
    setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
    }, 4000);
}
</script>
@endpush