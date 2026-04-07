@extends('layouts.app')

@section('title', 'Ticket de Venta')

@section('content')
<div class="container py-4">
    <!-- Alerta de Stock Bajo (Solo visible en pantalla) -->
    @if(session('alertas_stock'))
        @php
            $waNum = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'whatsapp_notificacion')->value('value');
            $msgStock = "*⚠️ AVISO DE REPOSICIÓN - " . ($config['nombre_kiosco'] ?? 'Kiosco') . "*\n\n";
            $msgStock .= "Los siguientes productos quedaron con stock crítico tras la última venta:\n";
            foreach(session('alertas_stock') as $alerta) {
                $msgStock .= "• " . $alerta . "\n";
            }
        @endphp
        <div class="row justify-content-center mb-4 no-print">
            <div class="col-md-6 col-lg-4">
                <div class="alert alert-danger shadow-sm border-2 animate__animated animate__shakeX">
                    <h5 class="alert-heading fw-bold mb-1 small text-uppercase"><i class="bi bi-exclamation-octagon-fill"></i> ¡PRODUCTOS AGOTADOS!</h5>
                    <ul class="mb-3 x-small list-unstyled ps-0">
                        @foreach(session('alertas_stock') as $alerta)
                            <li class="border-bottom border-danger-subtle py-1 small">• <strong>{{ $alerta }}</strong></li>
                        @endforeach
                    </ul>
                    @if($waNum)
                        <a href="https://wa.me/{{ $waNum }}?text={{ urlencode($msgStock) }}" target="_blank" class="btn btn-danger btn-sm w-100 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-whatsapp"></i> NOTIFICAR REPOSICIÓN
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <!-- TICKET ESTILO TÉRMICO -->
            <div class="ticket-termico shadow-lg bg-white p-4 mx-auto animate__animated animate__fadeInUp" id="ticket-print">
                <div class="text-center mb-3">
                    @php
                        $brandingLogo = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'logo')->value('value');
                        $brandingName = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'nombre_kiosco')->value('value') ?? 'Kiosco POS';
                        $brandingDir = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'direccion')->value('value');
                        $brandingTel = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'telefono')->value('value');
                    @endphp
                    
                    @if($brandingLogo)
                        <img src="{{ $brandingLogo }}" alt="Logo" class="img-fluid mb-2" style="max-height: 60px;">
                    @endif
                    <h4 class="fw-bold mb-0 text-uppercase">{{ $brandingName }}</h4>
                    <p class="small mb-0">{{ $brandingDir }}</p>
                    <p class="small mb-0">Tel: {{ $brandingTel }}</p>
                    <div class="border-bottom border-dark border-1 my-2"></div>
                    <p class="small fw-bold mb-0 text-uppercase">Comprobante de Venta</p>
                    <p class="small mb-0">{{ $venta->numero_venta }}</p>
                    <p class="small mb-0">{{ $venta->fecha_venta->format('d/m/Y H:i:s') }}</p>
                </div>

                <div class="ticket-body">
                    <table class="table table-sm table-borderless small">
                        <thead>
                            <tr class="border-bottom border-dark">
                                <th>DESCRIPCIÓN</th>
                                <th class="text-center">CANT</th>
                                <th class="text-end">SUB</th>
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
                            <tr>
                                <td style="max-width: 120px;" class="text-truncateSmall">
                                    {{ isset($nombresPersistidos[$index]) && !empty($nombresPersistidos[$index]) ? strtoupper($nombresPersistidos[$index]) : $detalle->producto->nombre }}
                                </td>
                                <td class="text-center">x{{ number_format($detalle->cantidad, 3) }}</td>
                                <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="border-bottom border-dark border-1 my-2"></div>
                    
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">SUBTOTAL:</span>
                        <span class="small">${{ number_format($venta->total + $venta->descuento, 2) }}</span>
                    </div>
                    @if($venta->descuento > 0)
                    <div class="d-flex justify-content-between mb-1 text-danger">
                        <span class="small">DESCUENTO:</span>
                        <span class="small">-${{ number_format($venta->descuento, 2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-2">
                        <span>TOTAL:</span>
                        <span>${{ number_format($venta->total, 2) }}</span>
                    </div>

                    <div class="border-bottom border-dark border-1 my-2"></div>

                    @if($venta->metodo_pago == 'efectivo')
                    <div class="d-flex justify-content-between small">
                        <span>EFECTIVO:</span>
                        <span>${{ number_format($venta->efectivo_recibido, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>VUELTO:</span>
                        <span>${{ number_format($venta->cambio, 2) }}</span>
                    </div>
                    @endif

                    <div class="text-center mt-4 pt-2 border-top border-dark border-dashed">
                        <p class="small mb-0 fw-bold">¡GRACIAS POR TU COMPRA!</p>
                        <p class="x-small mb-0">Atendido por: {{ $venta->user->name }}</p>
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="d-grid gap-3 mt-4 no-print">
                <button class="btn btn-primary btn-lg rounded-pill shadow" onclick="window.print()">
                    <i class="bi bi-printer-fill me-2"></i> IMPRIMIR TICKET
                </button>
                <div class="row g-2">
                    <div class="col-6">
                        <button class="btn btn-success btn-lg w-100 rounded-pill shadow" onclick="mostrarWhatsApp()">
                            <i class="bi bi-whatsapp"></i> WHATSAPP
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('ventas.create') }}" class="btn btn-dark btn-lg w-100 rounded-pill shadow">
                            <i class="bi bi-plus-circle"></i> NUEVA VENTA
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para que parezca ticket de papel */
    .ticket-termico {
        font-family: 'Courier New', Courier, monospace;
        width: 100%;
        color: #000;
        border: 1px solid #ddd;
    }
    .x-small { font-size: 0.75rem; }
    .border-dashed { border-style: dashed !important; }

    /* Ocultar todo lo que no sea el ticket al imprimir */
    @media print {
        body * { visibility: hidden; }
        #ticket-print, #ticket-print * { visibility: visible; }
        #ticket-print { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 80mm !important; /* Ancho estándar de ticketeras */
            box-shadow: none !important;
            border: none !important;
        }
        .no-print { display: none !important; }
        nav, .sidebar, .btn, .breadcrumb { display: none !important; }
    }
</style>

<div class="modal fade" id="whatsappModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title font-weight-bold"><i class="bi bi-whatsapp"></i> COMPARTIR TICKET</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small">NÚMERO DEL CLIENTE</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-phone"></i></span>
                        <input type="number" id="wa-phone" class="form-control bg-light border-0" 
                               placeholder="Ej: 5491122334455" required>
                    </div>
                    <small class="text-muted d-block mt-2">Ingresá código de país + código de área + número (Todo junto).</small>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">CANCELAR</button>
                <button type="button" onclick="enviarWA()" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">
                    ABRIR WHATSAPP
                </button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
function mostrarWhatsApp() {
    var myModal = new bootstrap.Modal(document.getElementById('whatsappModal'));
    myModal.show();
}

function enviarWA() {
    const telefono = document.getElementById('wa-phone').value;
    if(!telefono) { alert("Por favor ingresá un número"); return; }

    const kiosco = "{{ $brandingName }}";
    const nTicket = "{{ $venta->numero_venta }}";
    const total = "{{ number_format($venta->total, 2) }}";
    
    let mensaje = `*${kiosco}* \n`;
    mensaje += `*COMPROBANTE:* ${nTicket}\n`;
    mensaje += `Fecha: {{ $venta->fecha_venta->format('d/m/Y H:i') }}\n`;
    mensaje += `--------------------------\n`;
    
    @foreach($venta->detalles as $index => $detalle)
    @php
        $nombreJs = isset($nombresPersistidos[$index]) && !empty($nombresPersistidos[$index]) ? strtoupper($nombresPersistidos[$index]) : $detalle->producto->nombre;
    @endphp
    mensaje += `- {{ $nombreJs }} x{{ number_format($detalle->cantidad, 3) }} ($ {{ number_format($detalle->subtotal, 2) }})\n`;
    @endforeach
    
    mensaje += `--------------------------\n`;
    mensaje += `*TOTAL: $ ${total}*\n\n`;
    mensaje += `¡Gracias por tu compra! 😊`;

    const encoded = encodeURIComponent(mensaje);
    const win = window.open(`https://wa.me/${telefono}?text=${encoded}`, '_blank');
    if (win) win.focus();
}
</script>
@endpush
@endsection
