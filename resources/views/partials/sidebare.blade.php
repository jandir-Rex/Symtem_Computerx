<div class="sidebare-container">
    <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0 fw-bold text-center">
                <i class="fas fa-list me-2"></i>CATEGORÍAS
            </h5>
        </div>

        <!-- SCROLL INTERNO -->
        <div class="sidebare-scroll">
            <div class="list-group list-group-flush">

                @php
                    $cats = [
                        'laptops' => ['Laptops', 'laptop', ['asus'=>'ASUS','msi'=>'MSI','lenovo'=>'Lenovo','hp'=>'HP','dell'=>'Dell','acer'=>'Acer']],
                        'pc-gaming' => ['PC Gaming', 'desktop', ['intel'=>'Intel','amd'=>'AMD','rgb'=>'RGB']],
                        'componentes' => ['Componentes', 'microchip', ['asus'=>'ASUS','msi'=>'MSI','gigabyte'=>'Gigabyte','intel'=>'Intel','amd'=>'AMD']],
                        'perifericos' => ['Periféricos', 'keyboard', ['logitech'=>'Logitech','razer'=>'Razer','hyperx'=>'HyperX','corsair'=>'Corsair']],
                        'monitores' => ['Monitores', 'tv', ['teros'=>'Teros','samsung'=>'Samsung','lg'=>'LG','asus'=>'ASUS']],
                        'consolas' => ['Consolas', 'gamepad', ['ps'=>'PlayStation','xbox'=>'Xbox','nintendo'=>'Nintendo']],
                        'accesorios' => ['Accesorios', 'headset', ['logitech'=>'Logitech','razer'=>'Razer','hyperx'=>'HyperX','arctic'=>'Arctic']],
                        'repuestos' => ['Repuestos', 'tools', ['kingston'=>'Kingston','wd'=>'WD','samsung'=>'Samsung','corsair'=>'Corsair']],
                    ];
                @endphp

                @foreach($cats as $key => $data)
                    <div class="list-group-item border-0 px-0">
                        <a href="{{ route('category.' . $key) }}" 
                            class="d-flex align-items-center gap-2 px-3 py-2 fw-bold text-dark text-decoration-none hover-bg-primary">
                            <i class="fas fa-{{ $data[1] }} text-primary"></i>
                            {{ $data[0] }}
                        </a>
                        <div class="ps-4 pb-2">
                            @foreach($data[2] as $bkey => $bname)
                                <a href="{{ route('brand', ['category' => $key, 'brand' => $bkey]) }}" 
                                    class="d-block small py-1 text-decoration-none hover-brand-link"
                                    data-category="{{ $key }}" data-brand="{{ $bkey }}">
                                    <span class="text-dark">
                                        {{ $bname }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ESTILOS PRO -->
<style>
    .sidebare-container {
        position: sticky;
        top: 100px;
        align-self: start;
        z-index: 10;
    }

    .sidebare-scroll {
        max-height: calc(100vh - 180px);
        overflow-y: auto;
        overflow-x: hidden;
        padding: 0.5rem 0;
    }

    .sidebare-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .sidebare-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .sidebare-scroll::-webkit-scrollbar-thumb {
        background: #3783f6;
        border-radius: 10px;
    }
    .sidebare-scroll::-webkit-scrollbar-thumb:hover {
        background: #0b5ed7;
    }

    /* Hover para categorías */
    .hover-bg-primary {
        transition: all 0.2s ease;
    }
    .hover-bg-primary:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
        border-radius: 6px;
        padding-left: 1.25rem;
    }

    /* Hover para marcas */
    .hover-brand-link {
        transition: all 0.2s ease;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }
    .hover-brand-link:hover {
        background-color: rgba(255, 200, 0, 0.1) !important; /* amarillo claro */
        color: inherit !important;
        font-weight: 500;
    }

    /* Quitar subrayado de enlaces */
    .hover-brand-link span {
        text-decoration: none !important;
    }

    @media (max-width: 991.98px) {
        .sidebar-container {
            display: none !important;
        }
    }
</style>