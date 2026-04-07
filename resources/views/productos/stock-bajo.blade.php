@extends('layouts.app')

@section('title', 'Stock Bajo')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-exclamation-triangle text-danger"></i> Alerta de Stock Bajo</h2>
        <div>
            @role('admin')

            @php
                $waNum = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'whatsapp_notificacion')->value('value');
            @endphp
            @if($waNum && $productos->count() > 0)
                <button type="button" class="btn btn-success" onclick="enviarReporteWhatsApp('{{ $waNum }}')">
                    <i class="bi bi-whatsapp"></i> Mandar a mi WhatsApp
                </button>
            @endif
            @endrole
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    
    <div class="alert alert-warning">
        <i class="bi bi-info-circle"></i> 
        Se muestran los productos cuyo stock actual es igual o menor al stock mínimo establecido.
    </div>
    
    <div class="card">
        <div class="card-body">
            @if($productos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-danger">
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock Actual</th>
                            <th class="text-center">Stock Mínimo</th>
                            <th class="text-end">Precio</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr class="{{ $producto->stock == 0 ? 'table-danger' : '' }}">
                            <td>
                                <strong>{{ $producto->nombre }}</strong>
                                @if($producto->stock == 0)
                                    <span class="badge bg-danger ms-2">AGOTADO</span>
                                @endif
                            </td>
                            <td>
                                @if($producto->categoria)
                                    <span class="badge bg-secondary">{{ $producto->categoria }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $producto->stock == 0 ? 'danger' : 'warning' }} fs-6">
                                    {{ $producto->stock }}
                                </span>
                            </td>
                            <td class="text-center">
                                {{ $producto->stock_minimo }}
                            </td>
                            <td class="text-end">
                                ${{ number_format($producto->precio, 2) }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-box-seam"></i> Reponer
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success fs-1"></i>
                <h4 class="mt-3">¡Todo en orden!</h4>
                <p class="text-muted">No hay productos con stock bajo actualmente.</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script>
    function enviarReporteWhatsApp(numero) {
        const filas = document.querySelectorAll('tbody tr');
        let mensaje = "*🚀 REPORTE DE FALTANTES - {{ $config['nombre_kiosco'] ?? 'Kiosco POS' }}*\n";
        mensaje += "--------------------------------------\n";
        
        let hayProductos = false;
        filas.forEach(fila => {
            const nombre = fila.querySelector('td strong').innerText.trim();
            const stock = fila.querySelector('td:nth-child(3)').innerText.trim();
            const agotado = fila.innerText.includes('AGOTADO');
            
            mensaje += `${agotado ? '🔴' : '⚠️'} *${nombre}* (Stock: ${stock})\n`;
            hayProductos = true;
        });

        if (!hayProductos) {
            alert("No hay productos en la lista para enviar.");
            return;
        }

        mensaje += "--------------------------------------\n";
        mensaje += "_Generado automáticamente por tu Kiosco POS_";

        const url = `https://wa.me/${numero}?text=${encodeURIComponent(mensaje)}`;
        window.open(url, '_blank');
    }
</script>
@endpush
@endsection
