@extends('layouts.app')

@section('title', 'Gestión de Caja')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-cash-stack text-success"></i> Control de Caja</h2>
    </div>

    <div class="row">
        @forelse($cajas as $caja)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100 {{ $caja->abierta ? 'border-start border-4 border-success' : 'border-start border-4 border-secondary' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title fw-bold mb-0">{{ $caja->nombre }}</h5>
                        @if($caja->abierta)
                            <span class="badge bg-success rounded-pill px-3">ABIERTA</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-3">CERRADA</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <p class="text-muted small mb-1">Saldo Actual</p>
                        <h3 class="fw-bold {{ $caja->saldo_actual < 0 ? 'text-danger' : 'text-dark' }}">
                            {{ number_format($caja->saldo_actual, 2) }}
                        </h3>
                    </div>

                    @if($caja->abierta)
                        <div class="d-grid gap-2">
                            <a href="{{ route('cajas.show', $caja) }}" class="btn btn-outline-primary">
                                <i class="bi bi-eye"></i> Detalles y Movimientos
                            </a>
                            <form action="{{ route('cajas.cerrar', $caja) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Seguro que quieres cerrar la caja?')">
                                    <i class="bi bi-lock"></i> Cerrar Caja
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('cajas.abrir', $caja) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Saldo de Apertura</label>
                                <input type="number" name="saldo_apertura" step="0.01" class="form-control" value="0.00" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-unlock"></i> Abrir Caja Ahora
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info py-4 text-center">
                <i class="bi bi-info-circle fs-2 mb-2"></i>
                <p class="mb-0">No hay cajas configuradas en el sistema.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
