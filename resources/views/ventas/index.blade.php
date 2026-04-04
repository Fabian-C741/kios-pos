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
    
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="{{ $fecha }}" 
                           onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total del día</label>
                    <div class="form-control bg-light fw-bold">${{ number_format($totalDia, 2) }}</div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('ventas.reporte') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark-bar-graph"></i> Ver Reportes
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
                                <i class="bi bi-person"></i> {{ $venta->user->name }}
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
