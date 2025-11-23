@extends('layouts.app')

@section('title', 'Punto de Venta - Stand 1')

@section('content')
<div class="container mt-4">

    {{-- Volver al Dashboard --}}
    <div class="mb-3">
        <a href="{{ route('stands.stand1.dashboard') }}" 
           class="btn btn-outline-secondary fw-semibold d-inline-flex align-items-center gap-2">
            <i class="fas fa-arrow-left"></i> Volver al Dashboard
        </a>
    </div>

    <h2 class="mb-4 text-center fw-bold display-6 text-dark">
        <i class="fas fa-cash-register"></i> Punto de Venta
    </h2>

    {{-- Datos del Cliente --}}
    <div class="card shadow-sm mb-4 border-light">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0"><i class="fas fa-user"></i> Datos del Cliente</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" id="nombre_cliente" class="form-control" placeholder="Nombre del cliente" required>
                </div>
                <div class="col-md-6">
                    <input type="text" id="documento_cliente" class="form-control" placeholder="DNI/RUC (opcional)">
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label class="form-label">Tipo de comprobante: <strong id="tipoComprobanteLabel">Boleta</strong></label>
                </div>
            </div>

            {{-- Campo de celular dinámico (solo crédito) --}}
            <div class="row g-3 mt-2" id="campo_celular" style="display: none;">
                <div class="col-md-6">
                    <input type="text" id="celular_cliente" class="form-control" placeholder="Celular (requerido para crédito)">
                </div>
            </div>
        </div>
    </div>

    {{-- Búsqueda de Productos --}}
    <div class="card shadow-sm mb-4 border-light">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0"><i class="fas fa-search"></i> Buscar Producto</h5>
        </div>
        <div class="card-body position-relative">
            <input type="text" id="buscarProducto" class="form-control form-control-lg" placeholder="Nombre o código de barras" autocomplete="off">
            <ul class="list-group position-absolute w-100 mt-1" id="listaResultados" style="z-index: 1050; max-height: 300px; overflow-y: auto;"></ul>
        </div>
    </div>

    {{-- Carrito --}}
    <div class="card shadow-sm border-light">
        <div class="card-header bg-light text-dark">
            <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Carrito de Compra</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th style="width:180px;">Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="carrito">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Carrito vacío</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Totales con IGV --}}
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal:</span>
                        <span class="fw-semibold">S/ <span id="subtotal">0.00</span></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">IGV (18%):</span>
                        <span class="fw-semibold">S/ <span id="igv">0.00</span></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5 text-dark">Total:</span>
                        <span class="fw-bold fs-5 text-success">S/ <span id="total">0.00</span></span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex gap-2">
                    <select id="tipo_venta" class="form-select w-auto">
                        <option value="contado">Contado</option>
                        <option value="credito">Crédito</option>
                    </select>
                    <select id="tipo_pago" class="form-select w-auto">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
            </div>

            {{-- Campo dinámico para cuotas --}}
            <div id="divCuotas" class="mb-3" style="display: none;">
                <label for="num_cuotas" class="form-label">Número de cuotas</label>
                <input type="number" min="1" id="num_cuotas" class="form-control w-auto" placeholder="Ej: 3">
                <small class="text-muted" id="infoCuotas"></small>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-dark flex-fill fw-bold shadow-sm" id="btnGuardar" type="button">
                    <i class="fas fa-save"></i> Registrar Venta
                </button>
                <a href="{{ route('stands.stand1.dashboard') }}" class="btn btn-outline-secondary flex-fill fw-bold shadow-sm">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    'use strict';
    
    // ========== VARIABLES GLOBALES ==========
    let carrito = [];
    let timeoutBusqueda = null;
    let ultimaBusquedaId = 0;
    
    // ========== ELEMENTOS DOM ==========
    const $ = id => document.getElementById(id);
    const $$ = selector => document.querySelectorAll(selector);
    
    const elementos = {
        buscarInput: $('buscarProducto'),
        listaResultados: $('listaResultados'),
        carrito: $('carrito'),
        nombreCliente: $('nombre_cliente'),
        documentoCliente: $('documento_cliente'),
        celularCliente: $('celular_cliente'),
        tipoVenta: $('tipo_venta'),
        tipoPago: $('tipo_pago'),
        numCuotas: $('num_cuotas'),
        divCuotas: $('divCuotas'),
        campoCelular: $('campo_celular'),
        infoCuotas: $('infoCuotas'),
        tipoComprobanteLabel: $('tipoComprobanteLabel'),
        btnGuardar: $('btnGuardar'),
        subtotalSpan: $('subtotal'),
        igvSpan: $('igv'),
        totalSpan: $('total')
    };

    // ========== CSRF TOKEN ==========
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    // ========== UTILIDADES ==========
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function formatoPrecio(numero) {
        return parseFloat(numero || 0).toFixed(2);
    }

    // ========== EVENTOS: TIPO DE VENTA ==========
    elementos.tipoVenta.addEventListener('change', function() {
        const esCredito = this.value === 'credito';
        elementos.divCuotas.style.display = esCredito ? 'block' : 'none';
        elementos.campoCelular.style.display = esCredito ? 'block' : 'none';
        
        if (!esCredito) {
            elementos.numCuotas.value = '';
            elementos.celularCliente.value = '';
            elementos.infoCuotas.textContent = '';
        }
    });

    // ========== EVENTOS: CUOTAS ==========
    elementos.numCuotas.addEventListener('input', function() {
        const total = parseFloat(elementos.totalSpan.textContent) || 0;
        const cuotas = parseInt(this.value) || 0;
        
        if (cuotas > 0 && total > 0) {
            const montoCuota = (total / cuotas).toFixed(2);
            const hoy = new Date();
            const fechas = [];
            
            for (let i = 1; i <= cuotas; i++) {
                const fecha = new Date(hoy);
                fecha.setMonth(fecha.getMonth() + i);
                fechas.push(fecha.toLocaleDateString('es-PE'));
            }
            
            elementos.infoCuotas.textContent = `Monto por cuota: S/ ${montoCuota} | Fechas: ${fechas.join(', ')}`;
        } else {
            elementos.infoCuotas.textContent = '';
        }
    });

    // ========== EVENTOS: TIPO COMPROBANTE ==========
    elementos.documentoCliente.addEventListener('input', function() {
        const doc = this.value.trim();
        elementos.tipoComprobanteLabel.textContent = doc.length === 11 ? 'Factura' : 'Boleta';
    });

    // ========== BUSCAR PRODUCTOS ==========
    elementos.buscarInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(timeoutBusqueda);
        elementos.listaResultados.innerHTML = '';
        
        if (query.length === 0) return;
        
        if (query.length < 2) {
            elementos.listaResultados.innerHTML = '<li class="list-group-item text-muted">Escribe al menos 2 caracteres...</li>';
            return;
        }
        
        timeoutBusqueda = setTimeout(() => buscarProductos(query), 300);
    });

    // ========== BUSQUEDA CON ENTER (CÓDIGO DE BARRAS) ==========
    elementos.buscarInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = this.value.trim();
            
            if (query.length > 0) {
                buscarYAgregarDirecto(query);
            }
        }
    });

    // ========== CERRAR LISTA AL HACER CLICK FUERA ==========
    document.addEventListener('click', function(e) {
        if (!elementos.buscarInput.contains(e.target) && !elementos.listaResultados.contains(e.target)) {
            elementos.listaResultados.innerHTML = '';
        }
    });

    // ========== FUNCIÓN: BUSCAR PRODUCTOS ==========
    async function buscarProductos(query) {
        const busquedaId = ++ultimaBusquedaId;
        
        try {
            const response = await fetch(`/stand1/ventas/buscar?q=${encodeURIComponent(query)}`);
            
            if (!response.ok) throw new Error('Error en búsqueda');
            
            const productos = await response.json();
            
            if (busquedaId !== ultimaBusquedaId) return;
            
            console.log('Productos encontrados:', productos);
            
            if (productos.length === 0) {
                elementos.listaResultados.innerHTML = '<li class="list-group-item text-muted">No se encontraron productos</li>';
                return;
            }
            
            elementos.listaResultados.innerHTML = '';
            
            productos.forEach(producto => {
                const li = document.createElement('li');
                li.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                li.style.cursor = 'pointer';
                
                const precio = parseFloat(producto.precio_venta) || 0;
                
                li.innerHTML = `
                    <div>
                        <strong>${escapeHtml(producto.nombre)}</strong>
                        <small class="text-muted">(Stock: ${producto.stock})</small>
                    </div>
                    <span class="badge bg-primary">S/ ${formatoPrecio(precio)}</span>
                `;
                
                li.addEventListener('click', function() {
                    agregarProducto({
                        id: parseInt(producto.id),
                        nombre: String(producto.nombre),
                        precio_venta: precio,
                        stock: parseInt(producto.stock),
                        codigo_barras: producto.codigo_barras || ''
                    });
                    
                    elementos.buscarInput.value = '';
                    elementos.listaResultados.innerHTML = '';
                });
                
                elementos.listaResultados.appendChild(li);
            });
            
        } catch (error) {
            console.error('Error en búsqueda:', error);
            elementos.listaResultados.innerHTML = '<li class="list-group-item text-danger">Error al buscar</li>';
        }
    }

    // ========== FUNCIÓN: BUSCAR Y AGREGAR DIRECTO ==========
    async function buscarYAgregarDirecto(query) {
        try {
            const response = await fetch(`/stand1/ventas/buscar?q=${encodeURIComponent(query)}`);
            
            if (!response.ok) throw new Error('Error en búsqueda');
            
            const productos = await response.json();
            
            if (productos.length > 0) {
                const producto = productos[0];
                
                agregarProducto({
                    id: parseInt(producto.id),
                    nombre: String(producto.nombre),
                    precio_venta: parseFloat(producto.precio_venta) || 0,
                    stock: parseInt(producto.stock),
                    codigo_barras: producto.codigo_barras || ''
                });
                
                elementos.buscarInput.value = '';
                elementos.listaResultados.innerHTML = '';
            } else {
                alert('⚠️ Producto no encontrado');
            }
            
        } catch (error) {
            console.error('Error:', error);
            alert('Error al buscar producto');
        }
    }

    // ========== FUNCIÓN: AGREGAR PRODUCTO ==========
    function agregarProducto(producto) {
        console.log('Agregando producto:', producto);
        
        if (!producto.precio_venta || producto.precio_venta <= 0) {
            alert(`Error: Precio inválido para ${producto.nombre}`);
            return;
        }
        
        const existente = carrito.find(item => item.id === producto.id);
        
        if (existente) {
            if (existente.cantidad < producto.stock) {
                existente.cantidad++;
            } else {
                alert(`Stock máximo: ${producto.stock} unidades`);
                return;
            }
        } else {
            carrito.push({
                id: producto.id,
                nombre: producto.nombre,
                precio_venta: producto.precio_venta,
                stock: producto.stock,
                cantidad: 1
            });
        }
        
        renderizarCarrito();
    }

    // ========== FUNCIÓN: RENDERIZAR CARRITO ==========
    function renderizarCarrito() {
        console.log('Carrito actual:', carrito);
        
        if (carrito.length === 0) {
            elementos.carrito.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Carrito vacío</td></tr>';
            elementos.subtotalSpan.textContent = '0.00';
            elementos.igvSpan.textContent = '0.00';
            elementos.totalSpan.textContent = '0.00';
            return;
        }
        
        let totalConIGV = 0;
        elementos.carrito.innerHTML = '';
        
        carrito.forEach(item => {
            const precio = parseFloat(item.precio_venta);
            const subtotal = precio * item.cantidad;
            totalConIGV += subtotal;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${escapeHtml(item.nombre)}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.POS.disminuir(${item.id})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span class="btn btn-outline-secondary disabled">${item.cantidad}</span>
                        <button type="button" class="btn btn-outline-secondary" onclick="window.POS.aumentar(${item.id})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </td>
                <td class="text-end">S/ ${formatoPrecio(precio)}</td>
                <td class="text-end fw-semibold">S/ ${formatoPrecio(subtotal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger" onclick="window.POS.eliminar(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            elementos.carrito.appendChild(tr);
        });
        
        const subtotalSinIGV = totalConIGV / 1.18;
        const igv = totalConIGV - subtotalSinIGV;
        
        elementos.subtotalSpan.textContent = formatoPrecio(subtotalSinIGV);
        elementos.igvSpan.textContent = formatoPrecio(igv);
        elementos.totalSpan.textContent = formatoPrecio(totalConIGV);
    }

    // ========== FUNCIONES PÚBLICAS ==========
    window.POS = {
        aumentar: function(id) {
            const item = carrito.find(p => p.id === id);
            if (item && item.cantidad < item.stock) {
                item.cantidad++;
                renderizarCarrito();
            } else if (item) {
                alert(`Stock máximo: ${item.stock} unidades`);
            }
        },
        
        disminuir: function(id) {
            const item = carrito.find(p => p.id === id);
            if (item) {
                item.cantidad--;
                if (item.cantidad <= 0) {
                    this.eliminar(id);
                } else {
                    renderizarCarrito();
                }
            }
        },
        
        eliminar: function(id) {
            if (confirm('¿Eliminar este producto?')) {
                carrito = carrito.filter(p => p.id !== id);
                renderizarCarrito();
            }
        }
    };

    // ========== REGISTRAR VENTA ==========
    elementos.btnGuardar.addEventListener('click', async function() {
        const nombreCliente = elementos.nombreCliente.value.trim();
        const documentoCliente = elementos.documentoCliente.value.trim();
        const celularCliente = elementos.celularCliente.value.trim();
        const tipoVenta = elementos.tipoVenta.value;
        const tipoPago = elementos.tipoPago.value;
        const numCuotas = elementos.numCuotas.value;
        
        // Validaciones
        if (!nombreCliente) {
            alert('⚠️ Ingrese el nombre del cliente');
            elementos.nombreCliente.focus();
            return;
        }
        
        if (carrito.length === 0) {
            alert('⚠️ Agregue productos al carrito');
            elementos.buscarInput.focus();
            return;
        }
        
        if (tipoVenta === 'credito' && (!celularCliente || celularCliente.length < 6)) {
            alert('⚠️ Ingrese celular para ventas a crédito');
            elementos.celularCliente.focus();
            return;
        }
        
        if (tipoVenta === 'credito' && (!numCuotas || parseInt(numCuotas) < 1)) {
            alert('⚠️ Ingrese número de cuotas');
            elementos.numCuotas.focus();
            return;
        }
        
        const payload = {
            productos: carrito.map(p => ({
                id: p.id,
                cantidad: p.cantidad
            })),
            nombre_cliente: nombreCliente,
            documento_cliente: documentoCliente,
            celular_cliente: celularCliente || null,
            tipo_pago: tipoPago,
            tipo_venta: tipoVenta,
            num_cuotas: numCuotas ? parseInt(numCuotas) : null
        };
        
        console.log('Enviando venta:', payload);
        
        try {
            elementos.btnGuardar.disabled = true;
            elementos.btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';
            
            const response = await fetch('/stand1/ventas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.error || 'Error al registrar venta');
            }
            
            alert('✅ Venta registrada correctamente');
            window.location.href = `/stand1/ventas/${data.venta_id}`;
            
        } catch (error) {
            console.error('Error:', error);
            alert('⚠️ ' + error.message);
            elementos.btnGuardar.disabled = false;
            elementos.btnGuardar.innerHTML = '<i class="fas fa-save"></i> Registrar Venta';
        }
    });
    
    console.log('✅ POS inicializado correctamente');
})();
</script>
@endsection