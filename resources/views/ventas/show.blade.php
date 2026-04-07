@extends('layouts.app')

@section('title', 'Detalle de Venta ' . $venta->numero_venta)

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt-cutoff"></i> Detalle de Venta #{{ $venta->numero_venta }}</h2>
        <div>
            <a href="{{ route('ventas.ticket', $venta) }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-receipt"></i> Ver Ticket
            </a>
            <a href="{{ route('ventas.index') }}" class="btn btn-secondary shadow-sm">
                <i class="bi bi-arrow-left"></i> Volver al Historial
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información General -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle text-primary"></i> Información General</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Folio</span>
                            <strong class="fs-5">{{ $venta->numero_venta }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Fecha</span>
                            <strong>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Cajero asignado</span>
                            <strong><i class="bi bi-person"></i> {{ $venta->user->name ?? 'N/A' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Método de Pago</span>
                            <strong class="text-uppercase">{{ $venta->metodo_pago }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 border-0 mt-3 pt-3 border-top">
                            <span class="text-muted">Total Cobrado</span>
                            <strong class="text-success fs-3">${{ number_format($venta->total, 2) }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Lista de Productos -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-cart-check text-success"></i> Desglose de Productos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $nombresPersistidos = [];
                                    if ($venta->notas && str_contains($venta->notas, 'NAMES_JSON:')) {
                                        $partes = explode('NAMES_JSON:', $venta->notas);
                                        $nombresPersistidos = json_decode($partes[1], true) ?? [];
                                    }
                                @endphp
                                
                                @foreach($venta->detalles as $index => $detalle)
                                @php
                                    $nombreLimpio = isset($nombresPersistidos[$index]) && !empty($nombresPersistidos[$index]) ? strtoupper($nombresPersistidos[$index]) : $detalle->producto->nombre;
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-bold text-secondary">{{ $nombreLimpio }}</td>
                                    <td class="text-center">
                                        @if($detalle->producto->medida === 'kg')
                                            <span class="badge bg-light text-dark fw-bold border">{{ number_format($detalle->cantidad, 3) }} kg</span>
                                        @else
                                            <span class="badge bg-light text-dark fw-bold border">{{ $detalle->cantidad }} u</span>
                                        @endif
                                    </td>
                                    <td class="text-end text-muted">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="text-end pe-4 fw-bold text-dark">${{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
