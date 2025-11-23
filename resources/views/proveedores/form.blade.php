{{-- resources/views/proveedores/form.blade.php --}}

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="ruc" class="form-label fw-bold">
            RUC <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fas fa-id-card text-muted"></i>
            </span>
            <input 
                type="text" 
                name="ruc" 
                id="ruc" 
                class="form-control @error('ruc') is-invalid @enderror" 
                value="{{ old('ruc', $proveedor->ruc ?? '') }}" 
                required
                maxlength="11"
                pattern="[0-9]{11}"
                placeholder="20123456789"
            >
            @error('ruc')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> Debe tener exactamente 11 dígitos
        </small>
    </div>

    <div class="col-md-6 mb-3">
        <label for="nombre" class="form-label fw-bold">
            Nombre / Razón Social <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fas fa-building text-muted"></i>
            </span>
            <input 
                type="text" 
                name="nombre" 
                id="nombre" 
                class="form-control @error('nombre') is-invalid @enderror" 
                value="{{ old('nombre', $proveedor->nombre ?? '') }}" 
                required
                placeholder="DISTRIBUIDORA LIMA S.A.C."
            >
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="telefono" class="form-label fw-bold">
            Teléfono
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fas fa-phone text-muted"></i>
            </span>
            <input 
                type="text" 
                name="telefono" 
                id="telefono" 
                class="form-control @error('telefono') is-invalid @enderror" 
                value="{{ old('telefono', $proveedor->telefono ?? '') }}"
                placeholder="987654321"
            >
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <label for="email" class="form-label fw-bold">
            Email
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="fas fa-envelope text-muted"></i>
            </span>
            <input 
                type="email" 
                name="email" 
                id="email" 
                class="form-control @error('email') is-invalid @enderror" 
                value="{{ old('email', $proveedor->email ?? '') }}"
                placeholder="ventas@proveedor.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr class="my-4">

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('dashboard.proveedores.index') }}" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancelar
    </a>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> {{ isset($proveedor) ? 'Actualizar Proveedor' : 'Guardar Proveedor' }}
    </button>
</div>

@push('scripts')
<script>
// Validar que solo se ingresen números en el RUC
document.getElementById('ruc').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 11);
});

// Validar que solo se ingresen números en el teléfono
document.getElementById('telefono').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endpush