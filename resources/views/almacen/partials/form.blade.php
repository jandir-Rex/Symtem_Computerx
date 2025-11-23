<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-bold">Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Código de barras</label>
        <input type="text" name="codigo_barras" value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Precio compra</label>
        <input type="number" step="0.01" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? '') }}" class="form-control" required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Precio venta</label>
        <input type="number" step="0.01" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta ?? '') }}" class="form-control" required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Stock</label>
        <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Categoría</label>
        <select name="categoria" class="form-select">
            <option value="">Seleccionar...</option>
            @foreach($categorias as $key => $value)
                <option value="{{ $key }}" @selected(old('categoria', $producto->categoria ?? '') == $key)>
                    {{ $value }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Marca</label>
        <input type="text" name="marca" value="{{ old('marca', $producto->marca ?? '') }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Garantía (meses)</label>
        <input type="number" name="garantia_meses" value="{{ old('garantia_meses', $producto->garantia_meses ?? 0) }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Stock mínimo</label>
        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? 0) }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Imagen</label>
        <input type="file" name="imagen" class="form-control" accept="image/*" onchange="previewImagen(event)">
    </div>

    <div class="col-md-12">
        <label class="form-label fw-bold">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
    </div>

    <div class="col-md-12 d-flex gap-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="activo" value="1" @checked(old('activo', $producto->activo ?? true))>
            <label class="form-check-label">Activo</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="destacado" value="1" @checked(old('destacado', $producto->destacado ?? false))>
            <label class="form-check-label">Destacado</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="visible_ecommerce" value="1" @checked(old('visible_ecommerce', $producto->visible_ecommerce ?? false))>
            <label class="form-check-label">Visible en eCommerce</label>
        </div>
    </div>

    {{-- Imagen preview --}}
    <div class="col-md-12 text-center">
        <img id="preview" src="{{ isset($producto) && $producto->imagen ? asset('storage/' . $producto->imagen) : asset('images/producto-placeholder.png') }}"
             class="rounded mt-3" width="120">
    </div>

    <div class="col-md-12 text-end mt-4">
        <a href="{{ route('almacen.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar
        </button>
    </div>
</div>

<script>
function previewImagen(event) {
    const output = document.getElementById('preview');
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src);
    }
}
</script>
