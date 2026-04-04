<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $venta->numero_venta }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; width: 80mm; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .border-bottom { border-bottom: 1px dashed #000; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; }
        .right { text-align: right; }
        hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        .big { font-size: 16px; font-weight: bold; }
        .barcode { text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="text-center">
        <p class="big">{{ config('app.name', 'KIOSCO') }}</p>
        <p>================================</p>
        <p>FECHA: {{ $venta->fecha_venta->format('d/m/Y H:i') }}</p>
        <p>FOLIO: {{ $venta->numero_venta }}</p>
        <p>CAJA: {{ $venta->user->name }}</p>
        <p>================================</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th class="text-left">PRODUCTO</th>
                <th class="right">CANT</th>
                <th class="right">IMP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td>{{ substr($detalle->producto->nombre, 0, 20) }}</td>
                <td class="right">{{ $detalle->cantidad }}</td>
                <td class="right">${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <hr>
    
    <div>
        <div style="display: flex; justify-content: space-between;">
            <span>SUBTOTAL:</span>
            <span>${{ number_format($venta->total + $venta->descuento, 2) }}</span>
        </div>
        @if($venta->descuento > 0)
        <div style="display: flex; justify-content: space-between; color: red;">
            <span>DESCUENTO:</span>
            <span>-${{ number_format($venta->descuento, 2) }}</span>
        </div>
        @endif
        <div style="display: flex; justify-content: space-between;" class="fw-bold big">
            <span>TOTAL:</span>
            <span>${{ number_format($venta->total, 2) }}</span>
        </div>
    </div>
    
    @if($venta->metodo_pago == 'efectivo')
    <hr>
    <div>
        <div style="display: flex; justify-content: space-between;">
            <span>EFECTIVO:</span>
            <span>${{ number_format($venta->efectivo_recibido, 2) }}</span>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span>CAMBIO:</span>
            <span>${{ number_format($venta->cambio, 2) }}</span>
        </div>
    </div>
    @endif
    
    <hr>
    
    <div class="text-center">
        <p>Método: {{ strtoupper($venta->metodo_pago) }}</p>
    </div>
    
    <hr>
    
    <div class="text-center">
        <p>================================</p>
        <p>¡GRACIAS POR SU COMPRA!</p>
        <p>================================</p>
        <p class="small">Conserve este ticket</p>
        <p class="small">para cualquier reclamación</p>
    </div>
    
    <div class="barcode">
        <p>{{ $venta->numero_venta }}</p>
    </div>
</body>
</html>
