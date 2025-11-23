@extends('layouts.appe')

@section('title', 'Mi Carrito - Company Computer')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- BOTÓN VOLVER -->
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-pill mb-4 d-inline-block">
                <i class="fas fa-arrow-left"></i> Seguir comprando
            </a>

            <h1 class="fw-bold display-5 mb-4 text-center text-danger">
                <i class="fas fa-shopping-cart"></i> MI CARRITO
            </h1>

            @if(empty($cart) || count($cart) === 0)
                <div class="text-center py-5 bg-light rounded-4">
                    <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted">Tu carrito está vacío</h3>
                    <a href="{{ route('home') }}" class="btn btn-danger btn-lg px-5">
                        <i class="fas fa-fire"></i> Ver Ofertas
                    </a>
                </div>
            @else
                <!-- MENSAJE CORREGIDO: AHORA TODOS PUEDEN MODIFICAR -->
                <div class="alert alert-success text-center mb-4 border-0">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>¡Puedes modificar tu carrito libremente!</strong> 
                    Solo necesitas iniciar sesión para pagar.
                </div>

                <div class="card shadow-lg border-0">
                    <div class="card-body p-0">
                        @foreach($cart as $index => $item)
                            <div class="cart-item p-4 border-bottom d-flex align-items-start gap-4" data-index="{{ $index }}">
                                <img src="{{ asset($item['image']) }}" 
                                        class="rounded-3 shadow-sm" 
                                        style="width: 100px; height: 100px; object-fit: cover;"
                                        alt="{{ $item['name'] }}">

                                <div class="flex-grow-1">
                                    <h5 class="fw-bold mb-1">{{ $item['name'] }}</h5>
                                    <p class="text-muted small mb-2">
                                        Categoría: {{ ucfirst(str_replace('-', ' ', $item['category'])) }}
                                    </p>
                                    <p class="text-danger fw-bold fs-5 mb-0">
                                        S/ {{ number_format($item['price'], 0) }}
                                    </p>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <!-- CANTIDAD (AHORA FUNCIONA PARA TODOS) -->
                                    <div class="input-group" style="width: 130px;">
                                        <button class="btn btn-outline-danger decrease-qty" data-index="{{ $index }}">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="text" class="form-control text-center fw-bold qty-input" 
                                                    value="{{ $item['quantity'] }}" data-index="{{ $index }}" readonly>
                                        <button class="btn btn-outline-success increase-qty" data-index="{{ $index }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>

                                    <button class="btn btn-outline-danger btn-sm remove-item" data-index="{{ $index }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>

                                    <!-- PRECIO TOTAL POR PRODUCTO -->
                                    <div class="text-end ms-3">
                                        <p class="fs-4 fw-bold text-danger mb-0 product-total" data-index="{{ $index }}">
                                            S/ {{ number_format($item['price'] * $item['quantity'], 0) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- TOTAL GENERAL -->
                    <div class="card-footer bg-dark text-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="mb-0">TOTAL A PAGAR</h3>
                            <h2 class="mb-0 text-warning cart-total-amount">
                                S/ {{ number_format($total, 0) }}
                            </h2>
                        </div>
                        
                        <!-- BOTÓN DE PAGO CONDICIONAL -->
                        @if(Auth::check())
                            <a href="{{ route('checkout.index') }}" class="btn btn-success btn-lg w-100 shadow-lg">
                                <i class="fas fa-credit-card"></i> PROCEDER AL PAGO SEGURO
                            </a>
                        @else
                            <a href="{{ route('login') }}?redirect={{ urlencode(url()->current()) }}" class="btn btn-primary btn-lg w-100 shadow-lg">
                                <i class="fas fa-sign-in-alt"></i> INICIAR SESIÓN PARA PAGAR
                            </a>
                        @endif
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-success fw-bold">
                        <i class="fas fa-shield-alt"></i> Pago 100% seguro • Envío gratis a todo Lima
                    </small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // TOAST MEJORADO
    function showToast(message, type = 'success') {
        let toast = document.getElementById('cartToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'cartToast';
            toast.className = 'toast align-items-center text-bg-' + (type === 'danger' ? 'danger' : 'success') + ' border-0 position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body text-white fw-bold">
                        <i class="fas fa-${type === 'danger' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
                        <span>${message}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>`;
            document.body.appendChild(toast);
        }
        toast.querySelector('.toast-body span').textContent = message;
        new bootstrap.Toast(toast, { delay: 2500 }).show();
    }

    // ACTUALIZAR CANTIDAD
    function updateQuantity(index, newQty) {
        if (newQty < 1) return;

        fetch(`/cart/update/${index}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ quantity: newQty })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Actualizar input
                const input = document.querySelector(`.qty-input[data-index="${index}"]`);
                if (input) input.value = newQty;

                // Actualizar precio del producto
                const priceEl = document.querySelector(`.product-total[data-index="${index}"]`);
                if (priceEl && data.cart && data.cart[index]) {
                    priceEl.textContent = `S/ ${(data.cart[index].price * newQty).toLocaleString('es-PE')}`;
                }

                // Actualizar total general
                document.querySelector('.cart-total-amount').textContent = 
                    `S/ ${parseFloat(data.cart_total).toLocaleString('es-PE')}`;

                // Actualizar badge
                const badge = document.querySelector('#cart-count');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.style.display = data.cart_count > 0 ? 'inline-block' : 'none';
                }

                // Recargar si está vacío
                if (data.cart_count === 0) {
                    setTimeout(() => location.reload(), 800);
                }

                // Notificar a otros scripts
                document.dispatchEvent(new Event('cartUpdated'));
            } else {
                showToast('Error al actualizar cantidad', 'danger');
            }
        })
        .catch(() => showToast('Error de conexión', 'danger'));
    }

    // ELIMINAR PRODUCTO
    function removeItem(index) {
        if (!confirm('¿Eliminar este producto del carrito?')) return;

        fetch(`/cart/remove/${index}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`.cart-item[data-index="${index}"]`);
                item.style.transition = 'all 0.4s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(-30px)';
                setTimeout(() => item.remove(), 400);

                // Actualizar total
                document.querySelector('.cart-total-amount').textContent = 
                    `S/ ${parseFloat(data.cart_total).toLocaleString('es-PE')}`;

                // Actualizar badge
                const badge = document.querySelector('#cart-count');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.style.display = data.cart_count > 0 ? 'inline-block' : 'none';
                }

                showToast('Producto eliminado', 'danger');

                if (data.cart_count === 0) {
                    setTimeout(() => location.reload(), 800);
                }

                document.dispatchEvent(new Event('cartUpdated'));
            }
        })
        .catch(() => showToast('Error al eliminar', 'danger'));
    }

    // EVENTOS (FUNCIONAN PARA TODOS: LOGUEADOS Y NO LOGUEADOS)
    document.querySelectorAll('.increase-qty').forEach(btn => {
        btn.addEventListener('click', () => {
            const index = btn.dataset.index;
            const input = btn.closest('.input-group').querySelector('.qty-input');
            updateQuantity(index, parseInt(input.value) + 1);
        });
    });

    document.querySelectorAll('.decrease-qty').forEach(btn => {
        btn.addEventListener('click', () => {
            const index = btn.dataset.index;
            const input = btn.closest('.input-group').querySelector('.qty-input');
            const newQty = parseInt(input.value) - 1;
            if (newQty >= 1) updateQuantity(index, newQty);
        });
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', () => removeItem(btn.dataset.index));
    });

    // SINCRONIZAR BADGE AL CARGAR LA PÁGINA
    fetch('/cart/data', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            const badge = document.querySelector('#cart-count');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        });
});
</script>
@endpush