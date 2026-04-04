@extends('layouts.app')

@section('title', 'Ticket de Venta')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-receipt"></i> Ticket - {{ $venta->numero_venta }}</h2>
        <div>
            <a href="{{ route('ventas.ticket.pdf', $venta) }}" class="btn btn-danger">
                <i class="bi bi-file-pdf"></i> Descargar PDF
            </a>
            <button class="btn btn-success" onclick="mostrarWhatsApp()">
                <i class="bi bi-whatsapp"></i> Enviar WhatsApp
            </button>
            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center border-bottom pb-3 mb-3">
                        <h4>TIQUETE DE VENTA</h4>
                        <p class="mb-0">{{ config('app.name') }}</p>
                        <small class="text-muted">{{ $venta->fecha_venta->format('d/m/Y H:i:s') }}</small>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong>Folio:</strong> {{ $venta->numero_venta }}
                        </div>
                        <div class="col-6 text-end">
                            <strong>Caja:</strong> {{ $venta->user->name }}
                        </div>
                    </div>
                    
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Cant.</th>
                                <th class="text-end">Precio</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre }}</td>
                                <td class="text-end">{{ $detalle->cantidad }}</td>
                                <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-8">
                            <p class="mb-1">Subtotal:</p>
                            @if($venta->descuento > 0)
                                <p class="mb-1 text-danger">Descuento:</p>
                            @endif
                            <h4 class="mb-0">TOTAL:</h4>
                        </div>
                        <div class="col-4 text-end">
                            <p class="mb-1">${{ number_format($venta->total + $venta->descuento, 2) }}</p>
                            @if($venta->descuento > 0)
                                <p class="mb-1 text-danger">-${{ number_format($venta->descuento, 2) }}</p>
                            @endif
                            <h4 class="mb-0">${{ number_format($venta->total, 2) }}</h4>
                        </div>
                    </div>
                    
                    @if($venta->metodo_pago == 'efectivo')
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <p class="mb-1">Efectivo recibido:</p>
                            <p class="mb-0">Cambio:</p>
                        </div>
                        <div class="col-6 text-end">
                            <p class="mb-1">${{ number_format($venta->efectivo_recibido, 2) }}</p>
                            <p class="mb-0 fw-bold">${{ number_format($venta->cambio, 2) }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <hr>
                    <p class="text-center text-muted mb-0">
                        <small>Método de pago: <strong>{{ ucfirst($venta->metodo_pago) }}</strong></small>
                    </p>
                    
                    <div class="text-center mt-4">
                        <p class="mb-0">¡Gracias por su compra!</p>
                        <small class="text-muted">Conserve este ticket para cualquier reclamación</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir Ticket
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="whatsappModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-whatsapp text-success"></i> Enviar por WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ventas.ticket.whatsapp', $venta) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Número de teléfono</label>
                        <input type="text" name="telefono" class="form-control" 
                               placeholder="Ej: 5512345678" required>
                        <small class="text-muted">Incluye lada sin +</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-whatsapp"></i> Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function mostrarWhatsApp() {
    new bootstrap.Modal(document.getElementById('whatsappModal')).show();
}
</script>
@endpush
@endsection
