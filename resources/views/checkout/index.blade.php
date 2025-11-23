@extends('layouts.appe')
@section('title', 'Finalizar Compra | Company Computer')
@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold">Finalizar Compra</h1>
    <div class="row">
        <!-- Resumen del pedido -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h2 class="h5 mb-0">Resumen del Pedido</h2>
                </div>
                <div class="card-body">
                    <div class="list-group mb-4">
                        @forelse($cart as $item)
                            <div class="list-group-item d-flex align-items-center py-3">
                                @php
                                    // La imagen viene como URL completa o como ruta relativa
                                    $imageUrl = $item['image'] ?? null;
                                    
                                    // Si es una URL completa (http://...), extraer solo la parte relativa
                                    if ($imageUrl && str_starts_with($imageUrl, 'http')) {
                                        // Extraer todo después del dominio
                                        $imageUrl = parse_url($imageUrl, PHP_URL_PATH);
                                        $imageUrl = ltrim($imageUrl, '/');
                                    }
                                @endphp
                                
                                @if($imageUrl)
                                    <img src="{{ asset($imageUrl) }}" 
                                        alt="{{ $item['name'] ?? 'Producto' }}" 
                                        class="rounded border shadow-sm" 
                                        style="width: 80px; height: 80px; min-width: 80px; object-fit: cover;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    {{-- Fallback si falla la carga --}}
                                    <div class="d-none align-items-center justify-content-center rounded border bg-gradient" 
                                         style="width: 80px; height: 80px; min-width: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="fas fa-laptop fa-2x text-white"></i>
                                    </div>
                                @else
                                    {{-- Placeholder si no hay imagen --}}
                                    <div class="d-flex flex-column align-items-center justify-content-center rounded border bg-gradient" 
                                         style="width: 80px; height: 80px; min-width: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <i class="fas fa-laptop fa-2x text-white mb-1"></i>
                                        <small class="text-white" style="font-size: 0.6rem;">Producto</small>
                                    </div>
                                @endif
                                    
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-bold">{{ $item['name'] ?? 'Producto' }}</h6>
                                    <p class="text-muted mb-0 small">
                                        <i class="fas fa-tag me-1"></i>
                                        S/{{ number_format(floatval($item['price'] ?? 0), 2) }} c/u
                                    </p>
                                </div>
                                <div class="text-end ms-3">
                                    <span class="badge bg-secondary mb-2" style="font-size: 0.9rem;">
                                        <i class="fas fa-times me-1"></i>{{ $item['quantity'] ?? ($item['qty'] ?? 1) }}
                                    </span>
                                    <p class="fw-bold text-primary mb-0 fs-5">
                                        S/{{ number_format((floatval($item['price'] ?? 0) * ($item['quantity'] ?? ($item['qty'] ?? 1))), 2) }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted">No hay productos en el carrito.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>S/{{ number_format($subtotal ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>IGV (18%):</span>
                            <strong>S/{{ number_format($igv ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span class="fw-bold">Total:</span>
                            <strong class="fs-4 text-primary">S/{{ number_format($total ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de pago -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h2 class="h5 mb-0">Información de Envío y Pago</h2>
                </div>
                <div class="card-body">
                    @if(!Auth::check())
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-user me-2"></i>
                            Debes iniciar sesión para completar tu pedido.
                            <a href="{{ route('login') }}?redirect={{ urlencode(url()->full()) }}" class="btn btn-sm btn-primary mt-2">
                                Iniciar sesión
                            </a>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('checkout.process') }}" id="checkoutForm">
                        @csrf
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre completo *</label>
                                {{-- ✅ SOLO old() - SIN Auth::user() --}}
                                <input type="text" class="form-control" id="name" name="name" required 
                                    value="{{ old('name') }}" 
                                    placeholder="Ej: Juan Pérez García">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo electrónico *</label>
                                {{-- ✅ SOLO old() - SIN Auth::user() --}}
                                <input type="email" class="form-control" id="email" name="email" required 
                                    value="{{ old('email') }}" 
                                    placeholder="ejemplo@correo.com">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Teléfono *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required 
                                    value="{{ old('phone') }}" 
                                    placeholder="999 123 456">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dni" class="form-label">DNI / RUC</label>
                                <input type="text" class="form-control" id="dni" name="dni" 
                                    value="{{ old('dni') }}" 
                                    placeholder="12345678 o 20123456789">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Dirección de envío *</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required 
                                placeholder="Av. Principal 123, Urb. Los Jardines">{{ old('address') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="district" class="form-label">Distrito *</label>
                                <input type="text" class="form-control" id="district" name="district" required 
                                    value="{{ old('district') }}" 
                                    placeholder="Ej: Víctor Larco">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">Ciudad *</label>
                                <input type="text" class="form-control" id="city" name="city" required 
                                    value="{{ old('city', 'Trujillo') }}">
                            </div>
                        </div>

                        <h3 class="h5 mb-3 mt-4">Método de Pago</h3>
                        <div class="row g-3 mb-3">
                            <!-- STRIPE / TARJETA -->
                            <div class="col-md-6">
                                <div class="form-check border p-3 rounded bg-light">
                                    <input class="form-check-input" type="radio" id="stripe" name="payment_method" value="stripe" required {{ old('payment_method') == 'stripe' ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center" for="stripe">
                                        <i class="fab fa-cc-stripe me-2 text-primary" style="font-size: 28px;"></i>
                                        <strong>Tarjeta de Crédito/Débito</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-check border p-3 rounded bg-light">
                                    <input class="form-check-input" type="radio" id="yape" name="payment_method" value="yape" {{ old('payment_method') == 'yape' ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center" for="yape">
                                        <img src="{{ asset('images/bancos/yape.png') }}" alt="Yape" class="me-2" style="height: 28px;">
                                        <strong>Yape</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check border p-3 rounded bg-light">
                                    <input class="form-check-input" type="radio" id="plin" name="payment_method" value="plin" {{ old('payment_method') == 'plin' ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center" for="plin">
                                        <img src="{{ asset('images/bancos/plin.png') }}" alt="Plin" class="me-2" style="height: 28px;">
                                        <strong>Plin</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check border p-3 rounded bg-light">
                                    <input class="form-check-input" type="radio" id="transfer" name="payment_method" value="transfer" {{ old('payment_method') == 'transfer' ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center" for="transfer">
                                        <img src="{{ asset('images/bank.png') }}" alt="Transferencia" class="me-2" style="height: 28px;">
                                        <strong>Transferencia Bancaria</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check border p-3 rounded bg-light">
                                    <input class="form-check-input" type="radio" id="delivery" name="payment_method" value="delivery" {{ old('payment_method') == 'delivery' ? 'checked' : '' }}>
                                    <label class="form-check-label d-flex align-items-center" for="delivery">
                                        <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                        <strong>Pago contra entrega</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- DETALLES DINÁMICOS DE PAGO -->
                        <div id="payment-details" class="mt-4 p-4 bg-light rounded border" style="display:none;">

                            <!-- STRIPE -->
                            <div id="payment-stripe" style="display:none;">
                                <h5 class="text-primary">
                                    <i class="fab fa-cc-stripe me-2"></i>
                                    Pago con Tarjeta (Seguro)
                                </h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-lock me-2"></i>
                                    <strong>Pago 100% seguro</strong> procesado por Stripe. Aceptamos todas las tarjetas.
                                </div>
                                <p class="text-muted small">
                                    Serás redirigido a una página segura de pago. Tu información está protegida con encriptación SSL.
                                </p>
                            </div>

                            <!-- YAPE -->
                            <div id="payment-yape" style="display:none;">
                                <h5 class="text-success">Paga con Yape</h5>
                                <p>Escanea el QR o envía el monto exacto a:</p>
                                <h4 class="text-success fw-bold">999 123 456</h4>
                                <img src="{{ asset('images/bancos/qr/yape-qr.png') }}" alt="QR Yape" class="img-fluid rounded mt-3 shadow" style="max-width: 220px;">
                                <p class="mt-3 text-muted small">Envía el comprobante al WhatsApp: <a href="https://wa.me/51999123456" target="_blank">+51 999 123 456</a></p>
                            </div>

                            <!-- PLIN -->
                            <div id="payment-plin" style="display:none;">
                                <h5 class="text-info">Paga con Plin</h5>
                                <p>Escanea el QR o envía el monto exacto a:</p>
                                <h4 class="text-info fw-bold">987 654 321</h4>
                                <img src="{{ asset('images/bancos/qr/plin-qr.png') }}" alt="QR Plin" class="img-fluid rounded mt-3 shadow" style="max-width: 220px;">
                                <p class="mt-3 text-muted small">Envía el comprobante al WhatsApp: <a href="https://wa.me/51987654321" target="_blank">+51 987 654 321</a></p>
                            </div>

                            <!-- TRANSFERENCIA BANCARIA -->
                            <div id="payment-transfer" style="display:none;">
                                <h5>Transferencia Bancaria</h5>
                                <p>Realiza tu pago a cualquiera de estas cuentas:</p>

                                <div class="card mb-3 border-primary">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <img src="{{ asset('images/bancos/bcp.png') }}" alt="BCP" style="height: 40px;">
                                        <div>
                                            <h6>BANCO DE CRÉDITO DEL PERÚ (BCP)</h6>
                                            <p class="mb-1"><strong>SOLES:</strong> 570 93389779 0 62</p>
                                            <p class="mb-0"><strong>DÓLARES:</strong> 570 98576705 1 82</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h6 class="text-danger">Completa tus datos de transferencia:</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="bank_name" class="form-label">Banco *</label>
                                            <select class="form-select" id="bank_name" name="bank_name">
                                                <option value="">Selecciona banco</option>
                                                <option value="BCP">BCP</option>
                                                <option value="Interbank">Interbank</option>
                                                <option value="Scotiabank">Scotiabank</option>
                                                <option value="BBVA">BBVA</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="account_number" class="form-label">N° de operación *</label>
                                            <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Ej: 123456789">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_holder" class="form-label">Titular que transfirió *</label>
                                        <input type="text" class="form-control" id="account_holder" name="account_holder" placeholder="Nombre completo">
                                    </div>
                                </div>
                            </div>

                            <!-- PAGO CONTRA ENTREGA -->
                            <div id="payment-delivery" style="display:none;">
                                <h5 class="text-success">Pago contra entrega (Solo Trujillo)</h5>
                                <div class="alert alert-success">
                                    <i class="fas fa-truck"></i>
                                    <strong>¡Perfecto!</strong> Pagas al recibir tu pedido.
                                </div>
                                <p><strong>Formas de pago aceptadas:</strong> Efectivo, Yape o Plin</p>
                                <p><strong>Costo de envío:</strong> S/15.00 (ya incluido)</p>
                            </div>
                        </div>

                        <div class="form-check mb-4 mt-4">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required {{ old('terms') ? 'checked' : '' }}>
                            <label class="form-check-label" for="terms">
                                Acepto los <a href="{{ route('terms') }}" target="_blank">Términos y Condiciones</a> y la 
                                <a href="{{ route('privacy') }}" target="_blank">Política de Privacidad</a> *
                            </label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-3 fs-5 fw-bold" id="realizarPedidoBtn" {{ !Auth::check() ? 'disabled' : '' }}>
                            REALIZAR PEDIDO - S/{{ number_format($total ?? 0, 2) }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const paymentDetails = document.getElementById('payment-details');
    const paymentStripe = document.getElementById('payment-stripe');
    const paymentYape = document.getElementById('payment-yape');
    const paymentPlin = document.getElementById('payment-plin');
    const paymentTransfer = document.getElementById('payment-transfer');
    const paymentDelivery = document.getElementById('payment-delivery');

    function showPaymentDetails() {
        const selected = document.querySelector('input[name="payment_method"]:checked');
        if (!selected) {
            paymentDetails.style.display = 'none';
            return;
        }

        [paymentStripe, paymentYape, paymentPlin, paymentTransfer, paymentDelivery].forEach(el => {
            if (el) el.style.display = 'none';
        });

        const method = selected.value;
        if (method === 'stripe') paymentStripe.style.display = 'block';
        else if (method === 'yape') paymentYape.style.display = 'block';
        else if (method === 'plin') paymentPlin.style.display = 'block';
        else if (method === 'transfer') paymentTransfer.style.display = 'block';
        else if (method === 'delivery') paymentDelivery.style.display = 'block';

        paymentDetails.style.display = 'block';
    }

    paymentRadios.forEach(radio => radio.addEventListener('change', showPaymentDetails));
    showPaymentDetails();

    const form = document.getElementById('checkoutForm');
    const submitBtn = document.getElementById('realizarPedidoBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!document.getElementById('terms').checked) {
            Swal.fire('Error', 'Debes aceptar los Términos y Condiciones', 'error');
            return;
        }

        const required = ['name', 'email', 'phone', 'address', 'district', 'city'];
        let valid = true;
        required.forEach(id => {
            const field = document.getElementById(id);
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!valid) {
            Swal.fire('Campos incompletos', 'Por favor completa todos los campos obligatorios', 'warning');
            return;
        }

        const method = document.querySelector('input[name="payment_method"]:checked');
        if (!method) {
            Swal.fire('Método de pago', 'Selecciona un método de pago', 'warning');
            return;
        }

        if (method.value === 'transfer') {
            const bank = document.getElementById('bank_name');
            const op = document.getElementById('account_number');
            const titular = document.getElementById('account_holder');
            if (!bank.value || !op.value.trim() || !titular.value.trim()) {
                Swal.fire('Datos incompletos', 'Completa todos los datos de la transferencia', 'warning');
                return;
            }
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';

        setTimeout(() => {
            form.submit();
        }, 800);
    });

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Pedido realizado!',
            text: '{{ session('success') }}',
            timer: 5000
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
    @endif
});
</script>
@endpush