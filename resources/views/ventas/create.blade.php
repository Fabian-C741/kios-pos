@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cart-plus"></i> Nueva Venta</h2>
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-upc-scan"></i> Buscar Producto</h5>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-upc"></i></span>
                        <input type="text" id="codigo_barras" class="form-control" 
                               placeholder="Escanea o escribe el código de barras..." autofocus>
                        <button class="btn btn-primary" type="button" onclick="buscarProducto()">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-center">Stock</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="carrito">
                                <tr id="sin-productos">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="bi bi-cart3 fs-1"></i>
                                        <p class="mb-0 mt-2">Agrega productos al carrito</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-grid-3x3-gap"></i> Catálogo Rápido</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($productos as $producto)
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn btn-outline-secondary w-100 text-start p-2 producto-btn"
                                    onclick="agregarProducto({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->precio }}, {{ $producto->stock }})"
                                    {{ $producto->stock <= 0 ? 'disabled' : '' }}>
                                <div class="fw-bold text-truncate">{{ $producto->nombre }}</div>
                                <small class="text-muted">${{ number_format($producto->precio, 2) }}</small>
                                <span class="badge bg-{{ $producto->stock <= $producto->stock_minimo ? 'danger' : 'secondary' }} float-end">
                                    {{ $producto->stock }}
                                </span>
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Resumen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('ventas.store') }}" id="formVenta">
                        @csrf
                        <input type="hidden" name="productos_json" id="productos_json">
                        
                        <div class="mb-3">
                            <label class="form-label">Método de Pago</label>
                            <select name="metodo_pago" class="form-select" required>
                                <option value="efectivo">Efectivo</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="mixto">Mixto</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Descuento ($)</label>
                            <input type="number" name="descuento" id="descuento" class="form-control" 
                                   value="0" min="0" step="0.01" onchange="calcularTotal()">
                        </div>
                        
                        <div class="mb-3" id="efectivo-group">
                            <label class="form-label">Efectivo Recibido</label>
                            <input type="number" name="efectivo_recibido" id="efectivo_recibido" 
                                   class="form-control" value="0" min="0" step="0.01" onchange="calcularCambio()">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notas</label>
                            <textarea name="notas" class="form-control" rows="2"></textarea>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">$0.00</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Descuento:</span>
                            <span id="descuento-display">-$0.00</span>
                        </div>
                        
                        <div class="d-flex justify-content-between fs-4 fw-bold">
                            <span>TOTAL:</span>
                            <span id="total">$0.00</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2 text-success" id="cambio-group" style="display: none;">
                            <span>Cambio:</span>
                            <span id="cambio">$0.00</span>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 btn-lg mt-3" id="btn-vender" disabled>
                            <i class="bi bi-check-circle"></i> COBRAR
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let carrito = [];

function buscarProducto() {
    const codigo = document.getElementById('codigo_barras').value;
    if (!codigo) return;
    
    fetch(`/productos/buscar-codigo?codigo=${codigo}`)
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                agregarProducto(data.id, data.nombre, parseFloat(data.precio), data.stock);
            }
        })
        .catch(() => alert('Error al buscar producto'));
    
    document.getElementById('codigo_barras').value = '';
}

function agregarProducto(id, nombre, precio, stock) {
    const item = carrito.find(p => p.id === id);
    if (item) {
        if (item.cantidad < stock) {
            item.cantidad++;
        } else {
            alert('Stock máximo alcanzado');
        }
    } else {
        carrito.push({ id, nombre, precio, stock, cantidad: 1 });
    }
    renderCarrito();
}

function cambiarCantidad(id, delta) {
    const item = carrito.find(p => p.id === id);
    if (item) {
        item.cantidad += delta;
        if (item.cantidad <= 0) {
            carrito = carrito.filter(p => p.id !== id);
        } else if (item.cantidad > item.stock) {
            item.cantidad = item.stock;
            alert('Stock máximo alcanzado');
        }
    }
    renderCarrito();
}

function eliminarProducto(id) {
    carrito = carrito.filter(p => p.id !== id);
    renderCarrito();
}

function renderCarrito() {
    const tbody = document.getElementById('carrito');
    if (carrito.length === 0) {
        tbody.innerHTML = `
            <tr id="sin-productos">
                <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-cart3 fs-1"></i>
                    <p class="mb-0 mt-2">Agrega productos al carrito</p>
                </td>
            </tr>`;
        document.getElementById('btn-vender').disabled = true;
    } else {
        let html = '';
        carrito.forEach(item => {
            const subtotal = item.precio * item.cantidad;
            html += `
                <tr>
                    <td>${item.nombre}</td>
                    <td class="text-end">$${item.precio.toFixed(2)}</td>
                    <td class="text-center"><span class="badge bg-secondary">${item.stock}</span></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${item.id}, -1)">-</button>
                        <span class="mx-2">${item.cantidad}</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cambiarCantidad(${item.id}, 1)">+</button>
                    </td>
                    <td class="text-end fw-bold">$${subtotal.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarProducto(${item.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
        });
        tbody.innerHTML = html;
        document.getElementById('btn-vender').disabled = false;
    }
    calcularTotal();
}

function calcularTotal() {
    const subtotal = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const total = subtotal - descuento;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('descuento-display').textContent = '-$' + descuento.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
    
    document.getElementById('productos_json').value = JSON.stringify(carrito);
    calcularCambio();
}

function calcularCambio() {
    const efectivo = parseFloat(document.getElementById('efectivo_recibido').value) || 0;
    const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const totalFinal = total - descuento;
    
    if (efectivo >= totalFinal) {
        const cambio = efectivo - totalFinal;
        document.getElementById('cambio').textContent = '$' + cambio.toFixed(2);
        document.getElementById('cambio-group').style.display = 'flex';
    } else {
        document.getElementById('cambio-group').style.display = 'none';
    }
}

document.getElementById('codigo_barras').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        buscarProducto();
    }
});

document.getElementById('formVenta').addEventListener('submit', function() {
    document.getElementById('productos_json').value = JSON.stringify(carrito);
});
</script>
@endpush
@endsection
