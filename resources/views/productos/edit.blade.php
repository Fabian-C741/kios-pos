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
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Código de Barras</label>
                                <input type="text" name="codigo_barras" class="form-control @error('codigo_barras') is-invalid @enderror" 
                                       value="{{ old('codigo_barras', $producto->codigo_barras) }}">
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
                                <input type="number" name="stock" min="0" 
                                       class="form-control @error('stock') is-invalid @enderror" 
                                       value="{{ old('stock', $producto->stock) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Stock Mínimo *</label>
                                <input type="number" name="stock_minimo" min="0" 
                                       class="form-control @error('stock_minimo') is-invalid @enderror" 
                                       value="{{ old('stock_minimo', $producto->stock_minimo) }}" required>
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
@endsection
