@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cart3"></i> Ventas</h2>
        <a href="{{ route('ventas.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nueva Venta
        </a>
    </div>
    
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body p-3">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">BUSCAR FOLIO</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Ej: 0001" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-muted">FILTRAR POR FECHA</label>
                    <input type="date" name="fecha" class="form-control" value="{{ request('fecha', $fecha ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-success">TOTAL DEL DÍA</label>
                    <div class="form-control bg-light fw-bold text-success border-0">${{ number_format($totalDia, 2) }}</div>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        FILTRAR
                    </button>
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary shadow-sm" title="Limpiar">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th class="text-center">Productos</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Método</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ventas as $venta)
                        <tr>
                            <td>
                                <strong>{{ $venta->numero_venta }}</strong>
                            </td>
                            <td>
                                {{ $venta->fecha_venta->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <i class="bi bi-person"></i> {{ $venta->user->name ?? 'Usuario Eliminado' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $venta->detalles->count() }}</span>
                            </td>
                            <td class="text-end">
                                <strong>${{ number_format($venta->total, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                @switch($venta->metodo_pago)
                                    @case('efectivo')
                                        <span class="badge bg-success">Efectivo</span>
                                        @break
                                    @case('tarjeta')
                                        <span class="badge bg-primary">Tarjeta</span>
                                        @break
                                    @case('transferencia')
                                        <span class="badge bg-info">Transferencia</span>
                                        @break
                                    @case('mixto')
                                        <span class="badge bg-warning">Mixto</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-outline-secondary" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('ventas.ticket', $venta) }}" class="btn btn-outline-primary" title="Ticket">
                                        <i class="bi bi-receipt"></i>
                                    </a>
                                    <a href="{{ route('ventas.ticket.pdf', $venta) }}" class="btn btn-outline-danger" title="PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-cart-x fs-1"></i>
                                <p class="mb-0 mt-2">No hay ventas registradas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                {{ $ventas->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
