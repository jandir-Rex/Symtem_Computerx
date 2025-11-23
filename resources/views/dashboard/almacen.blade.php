@extends('layouts.dashboard')

@section('title', 'Gestión de Almacén - Admin')

@section('content')
<div class="container-fluid">

    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">
            <i class="fas fa-warehouse text-primary"></i> Gestión de Almacén
        </h3>
        <div>
            <button type="button" class="btn btn-success shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#modalImportar">
                <i class="fas fa-file-excel"></i> Importar Excel
            </button>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoProducto">
                <i class="fas fa-plus-circle"></i> Nuevo Producto
            </button>
        </div>
    </div>

    {{-- INDICADORES RÁPIDOS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-barcode fa-2x text-primary"></i>
                    <p class="mt-2 mb-1 text-muted small">Total SKUs</p>
                    <h4 class="fw-bold mb-0">{{ $stats['total_skus'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-cubes fa-2x text-success"></i>
                    <p class="mt-2 mb-1 text-muted small">Unidades Totales</p>
                    <h4 class="fw-bold mb-0">{{ $stats['total_unidades'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-store fa-2x text-info"></i>
                    <p class="mt-2 mb-1 text-muted small">Visible E-commerce</p>
                    <h4 class="fw-bold mb-0">
                        <span id="ecommerce-count">{{ $stats['visible_ecommerce'] }}</span>
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    <p class="mt-2 mb-1 text-muted small">Stock Bajo</p>
                    <h4 class="fw-bold mb-0">{{ $stats['alerta_stock_bajo'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.almacen.index') }}" id="formFiltros">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">
                            <i class="fas fa-search"></i> Buscar Producto
                        </label>
                        <input type="text" name="buscar" class="form-control" placeholder="Nombre o código..." value="{{ request('buscar') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold"><i class="fas fa-tags"></i> Categoría</label>
                        <select name="categoria" class="form-select" id="selectCategoria">
                            <option value="">Todas</option>
                            @foreach($categorias as $key => $value)
                                <option value="{{ $key }}" {{ request('categoria') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold"><i class="fas fa-store"></i> E-commerce</label>
                        <select name="visible_ecommerce" class="form-select" id="selectEcommerce">
                            <option value="">Todos</option>
                            <option value="1" {{ request('visible_ecommerce') == '1' ? 'selected' : '' }}>Visible</option>
                            <option value="0" {{ request('visible_ecommerce') == '0' ? 'selected' : '' }}>Oculto</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold"><i class="fas fa-toggle-on"></i> Estado</label>
                        <select name="activo" class="form-select" id="selectActivo">
                            <option value="">Todos</option>
                            <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-semibold"><i class="fas fa-boxes"></i> Stock</label>
                        <select name="stock_filtro" class="form-select" id="selectStock">
                            <option value="">Todos</option>
                            <option value="bajo" {{ request('stock_filtro') == 'bajo' ? 'selected' : '' }}>Stock Bajo</option>
                            <option value="sin_stock" {{ request('stock_filtro') == 'sin_stock' ? 'selected' : '' }}>Sin Stock</option>
                            <option value="con_stock" {{ request('stock_filtro') == 'con_stock' ? 'selected' : '' }}>Con Stock</option>
                        </select>
                    </div>

                    {{-- 🔹 Botón LIMPIAR (escoba) 🔹 --}}
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltros" title="Limpiar todos los filtros">
                            <i class="fas fa-broom"></i>
                        </button>
                    </div>

                    {{-- 🔹 Botón BUSCAR 🔹 --}}
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100" title="Aplicar filtros">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60">Imagen</th>
                            <th>Nombre</th>
                            <th width="120">Categoría</th>
                            <th width="100" class="text-center">Stock</th>
                            <th width="120" class="text-end">Precio Venta</th>
                            <th width="80" class="text-center">E-commerce</th>
                            <th width="80" class="text-center">Activo</th>
                            <th width="150" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr>
                            <td>
                                <img src="{{ $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/producto-placeholder.png') }}" 
                                     width="45" height="45" class="rounded" alt="{{ $producto->nombre }}">
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $producto->nombre }}</div>
                                @if($producto->codigo_barras)
                                    <small class="text-muted"><i class="fas fa-barcode"></i> {{ $producto->codigo_barras }}</small>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ ucfirst($producto->categoria) }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-{{ $producto->stock > $producto->stock_minimo ? 'success' : ($producto->stock > 0 ? 'warning' : 'danger') }}">
                                    {{ $producto->stock }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">S/ {{ number_format($producto->precio_venta, 2) }}</td>
                            
                            {{-- 🔥 TOGGLE E-COMMERCE UNIFICADO --}}
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
                                               style="cursor: pointer; transform: scale(1.2);">
                                    </div>
                                    <span id="badge-ecommerce-{{ $producto->id }}" 
                                          class="badge ms-2 {{ $producto->visible_ecommerce ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas {{ $producto->visible_ecommerce ? 'fa-eye' : 'fa-eye-slash' }} fa-xs"></i>
                                    </span>
                                </div>
                                @if(!$producto->activo || $producto->stock <= 0)
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle fa-xs me-1"></i>
                                        {{ !$producto->activo ? 'Inactivo' : 'Sin stock' }}
                                    </small>
                                @endif
                            </td>
                            
                            <td class="text-center">
                                @if($producto->activo)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </td>
                            <td class="text-end">
                                <button onclick="editarProducto({{ $producto->id }})" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-outline-danger" onclick="eliminarProducto({{ $producto->id }}, '{{ addslashes($producto->nombre) }}')" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                                <p>No hay productos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-3">
        {{ $productos->appends(request()->query())->links() }}
    </div>
</div>

{{-- 🆕 MODAL EDITAR --}}
<div class="modal fade" id="modalEditarProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarProducto" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Código de Barras</label>
                            <input type="text" name="codigo_barras" id="edit_codigo_barras" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Precio Compra *</label>
                            <input type="number" name="precio_compra" id="edit_precio_compra" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Precio Venta (sin IGV) *</label>
                            <input type="number" name="precio_venta" id="edit_precio_venta" class="form-control" step="0.01" required>
                            <small class="text-muted">Se agregará IGV automáticamente</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock *</label>
                            <input type="number" name="stock" id="edit_stock" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Mínimo *</label>
                            <input type="number" name="stock_minimo" id="edit_stock_minimo" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" id="edit_categoria" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($categorias as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" id="edit_marca" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Garantía (meses)</label>
                            <input type="number" name="garantia_meses" id="edit_garantia_meses" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="imagen" id="edit_imagen" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" id="edit_activo" value="1">
                                <label class="form-check-label">Activo</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="visible_ecommerce" id="edit_visible_ecommerce" value="1">
                                <label class="form-check-label">Visible E-commerce</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="destacado" id="edit_destacado" value="1">
                                <label class="form-check-label">Destacado</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL NUEVO --}}
<div class="modal fade" id="modalNuevoProducto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nuevo Producto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.almacen.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Nombre *</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Código de Barras</label>
                            <input type="text" name="codigo_barras" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Precio Compra *</label>
                            <input type="number" name="precio_compra" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Precio Venta *</label>
                            <input type="number" name="precio_venta" class="form-control" step="0.01" required>
                            <small class="text-muted">Se agregará IGV automáticamente</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock *</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock Mínimo *</label>
                            <input type="number" name="stock_minimo" class="form-control" value="5" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categoría</label>
                            <select name="categoria" class="form-select">
                                <option value="">Seleccionar...</option>
                                @foreach($categorias as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Garantía (meses)</label>
                            <input type="number" name="garantia_meses" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Imagen</label>
                            <input type="file" name="imagen" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="activo" value="1" checked>
                                <label class="form-check-label">Activo</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="visible_ecommerce" value="1">
                                <label class="form-check-label">Visible E-commerce</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="destacado" value="1">
                                <label class="form-check-label">Destacado</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL IMPORTAR --}}
<div class="modal fade" id="modalImportar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel"></i> Importar Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dashboard.almacen.importar') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Descarga la plantilla para conocer el formato.
                    </div>
                    <input type="file" name="archivo_excel" class="form-control" accept=".xlsx,.xls" required>
                    <a href="{{ route('dashboard.almacen.plantilla') }}" class="btn btn-info mt-3">
                        <i class="fas fa-download"></i> Descargar Plantilla
                    </a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ========================================
// TOGGLE E-COMMERCE ✅ UNIFICADO
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const ecommerceToggles = document.querySelectorAll('.toggle-ecommerce');
    
    ecommerceToggles.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const productoId = this.dataset.productoId;
            const isChecked = this.checked;
            const originalState = !isChecked;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            
            if (!csrfToken) {
                console.error('❌ CSRF token no encontrado');
                showNotification('Error: Token de seguridad no encontrado', 'danger');
                this.checked = originalState;
                return;
            }
            
            const token = csrfToken.getAttribute('content');
            
            // 🔥 RUTA UNIFICADA
            const url = `/almacen/${productoId}/toggle-ecommerce`;
            
            console.log('🔄 Toggle E-commerce (ADMIN):', {
                url: url,
                productoId: productoId,
                nuevoEstado: isChecked ? 'Visible' : 'Oculto'
            });
            
            this.disabled = true;
            const badge = document.querySelector(`#badge-ecommerce-${productoId}`);
            
            if (badge) {
                badge.innerHTML = '<i class="fas fa-spinner fa-spin fa-xs"></i>';
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
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (badge) {
                        if (isChecked) {
                            badge.classList.remove('bg-secondary', 'bg-warning');
                            badge.classList.add('bg-success');
                            badge.innerHTML = '<i class="fas fa-eye fa-xs"></i>';
                        } else {
                            badge.classList.remove('bg-success', 'bg-warning');
                            badge.classList.add('bg-secondary');
                            badge.innerHTML = '<i class="fas fa-eye-slash fa-xs"></i>';
                        }
                    }
                    
                    const counterElement = document.querySelector('#ecommerce-count');
                    if (counterElement) {
                        const currentCount = parseInt(counterElement.textContent) || 0;
                        const newCount = currentCount + (isChecked ? 1 : -1);
                        counterElement.textContent = newCount;
                        
                        counterElement.classList.add('text-primary', 'fw-bold');
                        setTimeout(() => {
                            counterElement.classList.remove('text-primary', 'fw-bold');
                        }, 1000);
                    }
                    
                    showNotification(data.message, 'success');
                    
                    const row = this.closest('tr');
                    if (row) {
                        row.classList.add('table-success');
                        setTimeout(() => {
                            row.classList.remove('table-success');
                        }, 1500);
                    }
                    
                } else {
                    this.checked = originalState;
                    
                    if (badge) {
                        if (originalState) {
                            badge.classList.remove('bg-secondary', 'bg-warning');
                            badge.classList.add('bg-success');
                            badge.innerHTML = '<i class="fas fa-eye fa-xs"></i>';
                        } else {
                            badge.classList.remove('bg-success', 'bg-warning');
                            badge.classList.add('bg-secondary');
                            badge.innerHTML = '<i class="fas fa-eye-slash fa-xs"></i>';
                        }
                    }
                    
                    showNotification(data.message || 'Error al cambiar visibilidad', 'danger');
                }
            })
            .catch(error => {
                console.error('❌ Error:', error);
                
                this.checked = originalState;
                
                if (badge) {
                    if (originalState) {
                        badge.classList.remove('bg-secondary', 'bg-warning');
                        badge.classList.add('bg-success');
                        badge.innerHTML = '<i class="fas fa-eye fa-xs"></i>';
                    } else {
                        badge.classList.remove('bg-success', 'bg-warning');
                        badge.classList.add('bg-secondary');
                        badge.innerHTML = '<i class="fas fa-eye-slash fa-xs"></i>';
                    }
                }
                
                showNotification('Error de conexión. Intenta nuevamente', 'danger');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});

// ========================================
// EDITAR PRODUCTO
// ========================================
function editarProducto(id) {
    fetch(`/dashboard/almacen/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const p = data.producto;
            
            document.getElementById('edit_nombre').value = p.nombre || '';
            document.getElementById('edit_codigo_barras').value = p.codigo_barras || '';
            document.getElementById('edit_precio_compra').value = p.precio_compra || '';
            
            const precioSinIGV = (p.precio_venta / 1.18).toFixed(2);
            document.getElementById('edit_precio_venta').value = precioSinIGV;
            
            document.getElementById('edit_stock').value = p.stock || '';
            document.getElementById('edit_stock_minimo').value = p.stock_minimo || '';
            document.getElementById('edit_categoria').value = p.categoria || '';
            document.getElementById('edit_marca').value = p.marca || '';
            document.getElementById('edit_garantia_meses').value = p.garantia_meses || '';
            document.getElementById('edit_descripcion').value = p.descripcion || '';
            
            document.getElementById('edit_activo').checked = p.activo == 1;
            document.getElementById('edit_destacado').checked = p.destacado == 1;
            document.getElementById('edit_visible_ecommerce').checked = p.visible_ecommerce == 1;
            
            document.getElementById('formEditarProducto').action = `/dashboard/almacen/${p.id}`;
            
            new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
        }
    })
    .catch(error => {
        Swal.fire('Error', 'No se pudo cargar el producto', 'error');
    });
}

// ========================================
// GUARDAR CAMBIOS CON AJAX
// ========================================
document.getElementById('formEditarProducto').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    Swal.fire({
        title: 'Guardando...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Error', data.message || 'Ocurrió un error', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'No se pudo actualizar', 'error');
    });
});

// ========================================
// ELIMINAR PRODUCTO
// ========================================
function eliminarProducto(id, nombre) {
    Swal.fire({
        title: '¿Eliminar producto?',
        html: `<p>Estás a punto de eliminar:</p><strong>${nombre}</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/dashboard/almacen/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// ========================================
// AUTO-SUBMIT FILTROS
// ========================================
['selectCategoria', 'selectEcommerce', 'selectActivo', 'selectStock'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('change', function() {
            document.getElementById('formFiltros').submit();
        });
    }
});

// ========================================
// LIMPIAR FILTROS CON ESCOBA
// ========================================
document.getElementById('btnLimpiarFiltros').addEventListener('click', function() {
    window.location.href = "{{ route('dashboard.almacen.index') }}";
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
    
    setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
        bsAlert.close();
    }, 4000);
}
</script>
@endpush