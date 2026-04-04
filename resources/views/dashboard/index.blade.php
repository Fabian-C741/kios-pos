@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-4">
    <h2 class="mb-4">
        <i class="bi bi-speedometer2"></i> Dashboard
    </h2>
    
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-cart3 fs-1 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Ventas Hoy</h6>
                            <h3 class="mb-0">{{ number_format($ventasHoyMonto, 2) }}</h3>
                            <small>{{ $ventasHoy }} transacciones</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('ventas.index') }}" class="card-footer text-white text-decoration-none">
                    Ver más <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-box-seam fs-1 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Total Stock</h6>
                            <h3 class="mb-0">{{ number_format($stockTotal) }}</h3>
                            <small>{{ $productosActivos }} productos activos</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('productos.index') }}" class="card-footer text-white text-decoration-none">
                    Ver más <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-graph-up fs-1 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Ventas Semana</h6>
                            <h3 class="mb-0">{{ number_format($ventasSemanaMonto, 2) }}</h3>
                            <small>{{ $ventasSemana }} transacciones</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('ventas.reporte') }}" class="card-footer text-dark text-decoration-none">
                    Ver más <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stat bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-currency-dollar fs-1 opacity-75"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">Inventario</h6>
                            <h3 class="mb-0">{{ number_format($valorInventario, 2) }}</h3>
                            <small>Valor total</small>
                        </div>
                    </div>
                </div>
                <a href="{{ route('productos.index') }}" class="card-footer text-white text-decoration-none">
                    Ver más <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-fire text-danger"></i> Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Vendidos</th>
                                    <th class="text-end">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productosMasVendidos as $producto)
                                <tr>
                                    <td>{{ $producto->nombre }}</td>
                                    <td class="text-end">${{ number_format($producto->precio, 2) }}</td>
                                    <td class="text-end">
                                        <span class="badge bg-success">{{ $producto->total_vendido ?? 0 }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($producto->stock_bajo)
                                            <span class="badge bg-danger">{{ $producto->stock }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $producto->stock }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Sin datos</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Stock Bajo</h5>
                </div>
                <div class="card-body p-0">
                    @if($productosStockBajo->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($productosStockBajo as $producto)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $producto->nombre }}</strong>
                                    <br><small class="text-muted">Mín: {{ $producto->stock_minimo }}</small>
                                </div>
                                <span class="badge bg-danger badge-stock">{{ $producto->stock }}</span>
                            </li>
                            @endforeach
                        </ul>
                        <div class="card-footer text-center">
                            <a href="{{ route('productos.stock-bajo') }}" class="btn btn-sm btn-outline-danger">
                                Ver todos <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check-circle fs-1"></i>
                            <p class="mb-0 mt-2">Todo el stock está en orden</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Ventas Recientes</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($ventasRecientes as $venta)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $venta->numero_venta }}</strong>
                                    <br><small class="text-muted">{{ $venta->user->name }}</small>
                                </div>
                                <div class="text-end">
                                    <strong>${{ number_format($venta->total, 2) }}</strong>
                                    <br><small class="text-muted">{{ $venta->fecha_venta->diffForHumans() }}</small>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">Sin ventas recientes</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
