@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="py-4">
    <nav aria-label="breadcrumb mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
            <li class="breadcrumb-item">{{ $producto->nombre }}</li>
            <li class="breadcrumb-item active">Editar</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Producto</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('productos.update', $producto) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre', $producto->nombre) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback fw-bold text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            
                             <div class="col-md-4">
                                <label class="form-label">Código de Barras</label>
                                <div class="input-group">
                                    <input type="text" name="codigo_barras" id="codigo_barras" class="form-control @error('codigo_barras') is-invalid @enderror" 
                                           value="{{ old('codigo_barras', $producto->codigo_barras) }}">
                                    <button class="btn btn-outline-primary" type="button" onclick="abrirEscaner()">
                                        <i class="bi bi-camera-fill"></i>
                                    </button>
                                </div>
                                @error('codigo_barras')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $producto->descripcion) }}</textarea>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Precio *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio" step="0.01" min="0" 
                                           class="form-control @error('precio') is-invalid @enderror" 
                                           value="{{ old('precio', $producto->precio) }}" required>
                                    @error('precio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Stock Actual *</label>
                                <input type="number" name="stock" min="0" step="0.001"
                                       class="form-control @error('stock') is-invalid @enderror" 
                                       value="{{ old('stock', $producto->stock) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback fw-bold text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Stock Mínimo *</label>
                                <input type="number" name="stock_minimo" min="0" step="0.001"
                                       class="form-control @error('stock_minimo') is-invalid @enderror" 
                                       value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
                                @error('stock_minimo')
                                    <div class="invalid-feedback fw-bold text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="categoria" class="form-control" 
                                       value="{{ old('categoria', $producto->categoria) }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" 
                                           {{ old('activo', $producto->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Producto activo</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <hr>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Actualizar Producto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Escáner -->
<div class="modal fade" id="escanerModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold">ESCANEAR CÓDIGO</h6>
                <button type="button" class="btn-close btn-close-white" onclick="cerrarEscaner()"></button>
            </div>
            <div class="modal-body p-0 bg-dark" style="min-height: 300px;">
                <div id="reader" style="width: 100%;"></div>
            </div>
            <div class="modal-footer py-1">
                <button type="button" class="btn btn-secondary btn-sm" onclick="cerrarEscaner()">CANCELAR</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let html5QrCode = null;
    const escanerModal = new bootstrap.Modal(document.getElementById('escanerModal'));
    const inputCodigo = document.getElementById('codigo_barras');
    const miProductoId = "{{ $producto->id }}"; // Guardamos el ID actual para ignorarlo

    function abrirEscaner() {
        escanerModal.show();
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start(
            { facingMode: "environment" }, 
            { 
                fps: 15, 
                qrbox: { width: 250, height: 150 } 
            },
            onScanSuccess
        ).catch(err => {
            console.error("No se pudo iniciar la cámara", err);
            alert("Error al acceder a la cámara. Asegúrate de dar permisos.");
            escanerModal.hide();
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        inputCodigo.value = decodedText;
        cerrarEscaner();
        if (navigator.vibrate) navigator.vibrate(100);
        // Verificar existencia inmediatamente después de escanear
        verificarExistencia(decodedText);
    }

    function cerrarEscaner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode = null;
                escanerModal.hide();
            }).catch(e => {
                html5QrCode = null;
                escanerModal.hide();
            });
        } else {
            escanerModal.hide();
        }
    }

    // Verificar cuando el usuario escribe a mano y sale del campo
    inputCodigo.addEventListener('blur', function() {
        if (this.value.trim() !== '') {
            verificarExistencia(this.value.trim());
        }
    });

    function verificarExistencia(codigo) {
        // No verificar si es muy corto
        if (codigo.length < 3) return;

        fetch(`{{ route('productos.buscar-codigo') }}?q=${codigo}`)
            .then(response => response.json())
            .then(data => {
                // Si encontramos un producto Y NO es el que estamos editando ahora
                if (data.success && data.id != miProductoId) {
                    Swal.fire({
                        title: '¡CÓDIGO EN USO!',
                        html: `Este código ya pertenece a otro producto:<br><br><span class="badge bg-danger fs-6">${data.nombre}</span><br><br>¿Seguro que quieres usar el mismo código?`,
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#e74a3b',
                        cancelButtonColor: '#858796',
                        confirmButtonText: 'USAR DE TODAS FORMAS',
                        cancelButtonText: 'CANCELAR Y VOLVER',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (!result.isConfirmed) {
                            // Si cancela, volvemos al código original del producto
                            inputCodigo.value = "{{ $producto->codigo_barras }}";
                        }
                    });
                }
            });
    }
</script>
@endpush
ion
