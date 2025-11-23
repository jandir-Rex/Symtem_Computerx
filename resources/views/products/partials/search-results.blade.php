@if($query === '')
    <p class="empty-cart">Escribe algo en la barra de búsqueda.</p>
@elseif(count($results) === 0)
    <p class="empty-cart">No se encontraron productos con "{{ $query }}".</p>
@else
    <div class="products-grid">
        @foreach($results as $product)
            <div class="product-card">
                <img src="{{ asset('images/' . $product['category'] . '/' . $product['image']) }}" alt="{{ $product['name'] }}">
                <div class="product-info">
                    <h3>{{ $product['name'] }}</h3>
                    <p class="price">S/{{ $product['price'] }}</p>
                    <small style="color: #666;">{{ $product['category_label'] }}</small>
                    <button class="add-to-cart"
                        data-name="{{ trim($product['name']) }}"
                        data-price="{{ trim($product['price']) }}"
                        data-image="{{ trim($product['category'] . '/' . $product['image']) }}"
                        data-category="{{ trim($product['category']) }}">
                        Añadir
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif