{{-- resources/views/egresos/form.blade.php --}}

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="documento_tipo" class="form-label fw-bold">Tipo de Documento <span class="text-danger">*</span></label>
        <select name="documento_tipo" id="documento_tipo" class="form-select @error('documento_tipo') is-invalid @enderror" required>
            <option value="">Seleccione...</option>
            @foreach($documentos as $doc)
                <option value="{{ $doc }}" {{ old('documento_tipo', $egreso->documento_tipo ?? '') == $doc ? 'selected' : '' }}>
                    {{ $doc }}
                </option>
            @endforeach
        </select>
        <small class="form-text text-muted">**Importante:** Solo las **FACTURAS** generan Crédito Fiscal (IGV a favor).</small>
        @error('documento_tipo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="proveedor_id" class="form-label fw-bold">Proveedor / Acreedor <span class="text-danger">*</span></label>
        <select name="proveedor_id" id="proveedor_id" class="form-select @error('proveedor_id') is-invalid @enderror" required>
            <option value="">Seleccione...</option>
            @foreach($proveedores as $id => $nombre)
                <option value="{{ $id }}" {{ old('proveedor_id', $egreso->proveedor_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $nombre }}
                </option>
            @endforeach
        </select>
        @error('proveedor_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="fecha_emision" class="form-label fw-bold">Fecha de Emisión <span class="text-danger">*</span></label>
        <input type="date" name="fecha_emision" id="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror" value="{{ old('fecha_emision', $egreso->fecha_emision ?? \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
        @error('fecha_emision')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="total" class="form-label fw-bold">Monto TOTAL (Incluye IGV) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">S/</span>
            <input type="number" step="0.01" name="total" id="total" class="form-control @error('total') is-invalid @enderror" value="{{ old('total', $egreso->total ?? '') }}" required placeholder="Ej: 150.50">
        </div>
        <small class="form-text text-muted">Este es el monto final del comprobante. El sistema calculará el IGV y el Subtotal.</small>
        @error('total')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="descripcion" class="form-label fw-bold">Descripción / Concepto del Gasto <span class="text-danger">*</span></label>
    <textarea name="descripcion" id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2" required>{{ old('descripcion', $egreso->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-grid gap-2 d-md-flex justify-content-md-end">
    <a href="{{ route('dashboard.egresos.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($egreso) ? 'Actualizar Egreso' : 'Guardar Nuevo Egreso' }}
    </button>
</div>
