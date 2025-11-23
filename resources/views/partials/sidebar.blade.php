<div id="sidebar" class="sidebar">
    <div class="p-3 text-white">
        <h5 class="fw-bold mb-4">Menú Principal</h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('stand1.dashboard') }}" class="nav-link text-white">
                    <i class="bi bi-speedometer2 me-2"></i> Stand 1
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('stand2.dashboard') }}" class="nav-link text-white">
                    <i class="bi bi-speedometer me-2"></i> Stand 2
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('stand1.ventas.pos') }}" class="nav-link text-white">
                    <i class="bi bi-cart4 me-2"></i> POS Stand 1
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="{{ route('stand2.ventas.pos') }}" class="nav-link text-white">
                    <i class="bi bi-cart-check me-2"></i> POS Stand 2
                </a>
            </li>
        </ul>
    </div>
</div>
