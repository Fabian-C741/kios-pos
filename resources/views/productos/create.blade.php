@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="py-4">
    <nav aria-label="breadcrumb mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
            <li class="breadcrumb-item active">Crear</li>
        </ol>
    </nav>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Nuevo Producto</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('productos.store') }}">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Código de Barras</label>
                                <input type="text" name="codigo_barras" class="form-control @error('codigo_barras') is-invalid @enderror" 
                                       value="{{ old('codigo_barras') }}" placeholder="Ej: 7501234567890">
                                @error('codigo_barras')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion') }}</textarea>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Precio *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio" step="0.01" min="0" 
                                           class="form-control @error('precio') is-invalid @enderror" 
                                           value="{{ old('precio', 0) }}" required>
                                    @error('precio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Stock Inicial *</label>
                                <input type="number" name="stock" min="0" 
                                       class="form-control @error('stock') is-invalid @enderror" 
                                       value="{{ old('stock', 0) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">Stock Mínimo *</label>
                                <input type="number" name="stock_minimo" min="0" 
                                       class="form-control @error('stock_minimo') is-invalid @enderror" 
                                       value="{{ old('stock_minimo', 5) }}" required>
                                @error('stock_minimo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="categoria" class="form-control" 
                                       value="{{ old('categoria') }}" placeholder="Ej: Bebidas, Snacks">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="activo" class="form-check-input" id="activo" value="1" 
                                           {{ old('activo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Producto activo</label>
                                </div>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <hr>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Guardar Producto
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
