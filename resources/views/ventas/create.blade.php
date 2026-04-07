@extends('layouts.app')

@section('title', 'Punto de Venta Profesional')

@section('content')
<div class="container-fluid py-4 pb-5">

    <!-- ALERTAS DE SESIÓN -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show fw-bold shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show fw-bold shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row mb-3 align-items-center">
        <div class="col">
            <h1 class="fw-bold text-primary mb-0"><i class="bi bi-cart4"></i> CAJA <small class="text-muted fs-6">v2.3</small></h1>
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-outline-primary rounded-pill px-3 fw-bold" type="button" onclick="ventaManual()">
                <i class="bi bi-pencil-square"></i> MANUAL
            </button>
            <button class="btn btn-primary rounded-pill px-4 shadow-lg fw-bold" type="button" onclick="toggleCamera()">
                <i class="bi bi-camera-fill me-1"></i> ESCÁNER
            </button>
        </div>
    </div>
    
    <!-- CONTENEDOR DEL ESCÁNER ADAPTATIVO -->
    <div id="reader-container" class="mb-4 d-none animate__animated animate__fadeIn">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden position-relative" style="background: #000;">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-2 px-3">
                <span class="small fw-bold"><i class="bi bi-upc-scan me-1"></i> LECTOR ACTIVO</span>
                <button type="button" class="btn-close btn-close-white" onclick="toggleCamera()"></button>
            </div>
            
            <div id="reader" style="width: 100% !important; border:none !important;"></div>
            
            <!-- OVERLAY DE BLOQUEO (ANTIREPETICION) -->
            <div id="scanner-lock-overlay" class="d-none position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center" 
                 style="background: rgba(0,0,0,0.85); z-index: 2000; backdrop-filter: blur(5px);">
                <div class="spinner-grow text-success mb-3" style="width: 3rem; height: 3rem;"></div>
                <h2 class="text-white fw-bold mb-1">¡PROCESANDO!</h2>
                <p class="text-success fw-bold">No mueva el producto...</p>
            </div>

            <div id="loader-scanner" class="d-none position-absolute top-50 start-50 translate-middle" style="z-index: 2001;">
                <div class="spinner-border text-primary" role="status"></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- BUSCADOR MANUAL -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="p-3 bg-light border-bottom">
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border bg-white">
                        <span class="input-group-text bg-white border-0 ps-4"><i class="bi bi-search text-primary"></i></span>
                        <input type="text" id="master-search" class="form-control border-0 py-3" 
                               placeholder="Código de barras o nombre..." autocomplete="off">
                        <button class="btn btn-primary px-4 fw-bold" type="button" onclick="const v = document.getElementById('master-search').value; if(v) buscarDual(v);">
                            AGREGAR
                        </button>
                    </div>
                    <div id="msg-error" class="alert alert-danger d-none mt-3 py-2 fw-bold shadow-sm animate__animated animate__shakeX"></div>
                </div>

                <!-- LISTADO DE PRODUCTOS -->
                <div class="table-responsive" style="min-height: 400px; background: white;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr class="small fw-bold text-uppercase">
                                <th class="ps-4 py-3">Detalle</th>
                                <th class="text-end py-3">Precio</th>
                                <th class="text-center py-3">Cantidad / Peso</th>
                                <th class="text-end py-3">Subtotal</th>
                                <th class="pe-4 text-center py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="cart-items">
                            <!-- JS RENDER -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PANEL DE COBRO -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden sticky-top" style="top: 1rem;">
                <div class="bg-primary p-4 text-center text-white shadow">
                    <span class="small opacity-75 fw-bold text-uppercase d-block mb-1">Total del Carrito</span>
                    <h2 class="display-3 fw-bold mb-0" id="cart-total">$0.00</h2>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('ventas.store') }}" method="POST" id="venta-form">
                        @csrf
                        <input type="hidden" name="productos_json" id="json_data">
                        <input type="hidden" name="metodo_pago" value="efectivo">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Paga con (F2)</label>
                            <div class="input-group input-group-lg shadow-sm border rounded-3 overflow-hidden">
                                <span class="input-group-text bg-light border-0 fw-bold text-success">$</span>
                                <input type="number" id="cash-received" name="efectivo_recibido" class="form-control text-center border-0 fw-bold fs-3" placeholder="0.00">
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center mb-4 border">
                            <span class="fw-bold text-muted">VUELTO:</span>
                            <span class="fw-bolder fs-4 text-success" id="cash-change">$0.00</span>
                        </div>

                        <button type="submit" id="btn-submit" class="btn btn-success btn-lg w-100 py-3 rounded-pill fw-bold shadow-lg fs-3 mb-2" disabled>
                            <i class="bi bi-cash-stack me-2"></i> COBRAR (F10)
                        </button>
                        <p class="text-center small text-muted mb-0">Atención: No se puede cobrar sin productos.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #reader video { object-fit: cover !important; width: 100% !important; min-height: 350px !important; }
    .btn-success { background: #28a745; border: none; }
    .ls-1 { letter-spacing: 1.5px; }
    .qty-input { width: 80px; text-align: center; border: 1px solid #ddd; border-radius: 20px; font-weight: bold; }
    @media (max-width: 576px) { .display-3 { font-size: 2.8rem; } }
</style>

@push('js')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let cart = [];
let html5QrCode = null;
let isProcessing = false;

function playBeep() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, audioCtx.currentTime);
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.1);
    } catch (e) {}
}

function onScanSuccess(txt) {
    if (isProcessing) return;
    isProcessing = true;
    document.getElementById('scanner-lock-overlay').classList.remove('d-none');
    playBeep();
    if (navigator.vibrate) navigator.vibrate(100);
    buscarDual(txt);
}

async function buscarDual(codigo) {
    const loader = document.getElementById('loader-scanner');
    const msgError = document.getElementById('msg-error');
    if(!isProcessing) loader.classList.remove('d-none');
    msgError.classList.add('d-none');

    try {
        const response = await fetch(`{{ route('productos.buscar-codigo') }}?q=${encodeURIComponent(codigo)}&_v=${Date.now()}`);
        const data = await response.json();
        
        if (data.success) {
            add(data.id, data.nombre, data.precio, data.stock);
            document.getElementById('master-search').value = "";
        } else {
            reproducirError();
            msgError.innerText = data.error || "No encontrado";
            msgError.classList.remove('d-none');
        }
    } catch (err) {
        msgError.innerText = "Error de conexión";
        msgError.classList.remove('d-none');
    } finally {
        loader.classList.add('d-none');
        setTimeout(() => {
            isProcessing = false;
            document.getElementById('scanner-lock-overlay').classList.add('d-none');
        }, 2000); 
    }
}

function add(id, nombre, precio, stock) {
    const existing = cart.find(p => p.id === id);
    if (existing) {
        existing.cantidad++;
    } else {
        cart.push({ id, nombre, precio: parseFloat(precio), cantidad: 1.000, stock: parseFloat(stock) });
    }
    render();
}

function ventaManual() {
    const nombre = prompt("Nombre del artículo:", "Varios");
    if (!nombre) return;
    const precio = prompt("Precio total ($):", "0");
    if (!precio || isNaN(precio)) return;
    
    // ID temporal único para no chocar con el producto ID 1 real y permitir múltiples ventas libres
    const uniqueId = 'MANUAL_' + Date.now();
    cart.push({ id: uniqueId, nombre: nombre.toUpperCase(), precio: parseFloat(precio), cantidad: 1.000, stock: 999 });
    render();
}

function calcularPorMonto(id) {
    const item = cart.find(p => p.id === id);
    if (!item) return;
    const monto = prompt(`¿Cuánto quiere llevar de ${item.nombre} en pesos ($)?`, "0");
    if (monto && !isNaN(monto)) {
        item.cantidad = parseFloat(monto) / item.precio;
        render();
    }
}

function changeQty(id, val) {
    const item = cart.find(p => p.id === id);
    if (item) {
        item.cantidad = parseFloat(val);
        if (item.cantidad <= 0) cart = cart.filter(p => p.id !== id);
        render();
    }
}

function render() {
    const list = document.getElementById('cart-items');
    if (cart.length === 0) {
        list.innerHTML = `<tr><td colspan="5" class="text-center py-5 opacity-50"><i class="bi bi-basket2 display-1 d-block mb-2"></i><h4 class="fw-bold">CARRITO VACÍO</h4></td></tr>`;
        document.getElementById('btn-submit').disabled = true;
    } else {
        list.innerHTML = cart.map(p => `
            <tr class="animate__animated animate__fadeInUp">
                <td class="ps-4">
                    <div class="fw-bold text-dark fs-5 line-height-1">${p.nombre}</div>
                    <small class="text-muted">Disp: ${p.stock.toFixed(3)}</small>
                </td>
                <td class="text-end fw-bold">$${p.precio.toFixed(2)}</td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <input type="number" step="0.001" class="qty-input py-1" value="${p.cantidad.toFixed(3)}" 
                               onchange="changeQty(${p.id}, this.value)">
                        <button type="button" class="btn btn-sm btn-outline-success rounded-circle" onclick="calcularPorMonto(${p.id})" title="Calcular por pesos">
                            <i class="bi bi-currency-dollar"></i>
                        </button>
                    </div>
                </td>
                <td class="text-end fw-bold text-primary fs-5">$${(p.precio * p.cantidad).toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-link text-danger" onclick="changeQty(${p.id}, 0)"><i class="bi bi-trash-fill fs-4"></i></button>
                </td>
            </tr>
        `).join('');
        document.getElementById('btn-submit').disabled = false;
    }
    calc();
}

function calc() {
    const total = cart.reduce((s, p) => s + (p.precio * p.cantidad), 0);
    document.getElementById('cart-total').textContent = '$' + total.toFixed(2);
    document.getElementById('json_data').value = JSON.stringify(cart);
    const cash = parseFloat(document.getElementById('cash-received').value) || 0;
    const change = cash > total ? cash - total : 0;
    document.getElementById('cash-change').textContent = '$' + change.toFixed(2);
}

document.getElementById('cash-received').oninput = calc;

function toggleCamera() {
    playBeep();
    const container = document.getElementById('reader-container');
    if (!html5QrCode) {
        container.classList.remove('d-none');
        html5QrCode = new Html5Qrcode("reader");
        html5QrCode.start({ facingMode: "environment" }, { 
            fps: 15, 
            qrbox: (w, h) => { let s = Math.min(w, h) * 0.7; return { width: s, height: s }; },
            aspectRatio: 1.0
        }, onScanSuccess);
    } else {
        html5QrCode.stop().then(() => { html5QrCode = null; container.classList.add('d-none'); });
    }
}

document.getElementById('master-search').addEventListener('keypress', (e) => { 
    if (e.key === 'Enter') { const val = e.target.value.trim(); if(val) buscarDual(val); } 
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'F10' && cart.length > 0) { e.preventDefault(); document.getElementById('venta-form').submit(); }
    if (e.key === 'F2') { e.preventDefault(); document.getElementById('cash-received').focus(); }
});

function reproducirError() { if (navigator.vibrate) navigator.vibrate([100, 50, 100]); }
render();
</script>
@endpush
@endsection
