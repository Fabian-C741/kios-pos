@extends('layouts.app')

@section('title', 'Ajustes del Sistema')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-gear-fill text-secondary"></i> Configuración General</h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold text-primary">Identidad del Negocio</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Nombre del Kiosco / Empresa</label>
                                <input type="text" name="nombre_kiosco" class="form-control form-control-lg bg-light" 
                                       value="{{ $config['nombre_kiosco'] ?? 'Kiosco POS' }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Símbolo de Moneda</label>
                                <input type="text" name="moneda" class="form-control bg-light" 
                                       value="{{ $config['moneda'] ?? '$' }}" required maxlength="10">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Teléfono de Contacto</label>
                                <input type="text" name="telefono" class="form-control bg-light" 
                                       value="{{ $config['telefono'] ?? '' }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-success text-uppercase"><i class="bi bi-whatsapp"></i> WhatsApp para Alertas</label>
                                <input type="text" name="whatsapp_notificacion" class="form-control bg-light border-success-subtle" 
                                       value="{{ $config['whatsapp_notificacion'] ?? '' }}" placeholder="Ej: 5491112345678">
                                <small class="text-muted small">Usar formato internacional (ej: 549...).</small>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Dirección del Establecimiento</label>
                                <textarea name="direccion" class="form-control bg-light" rows="2">{{ $config['direccion'] ?? '' }}</textarea>
                            </div>

                            <!-- Logo del Negocio -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Logo del Negocio (Sidebar)</label>
                                <div class="d-flex align-items-center gap-3 bg-light p-2 rounded-3">
                                    @if(isset($config['logo']) && $config['logo'])
                                        <img src="{{ $config['logo'] }}" alt="Logo" class="img-thumbnail" style="height: 50px;">
                                    @endif
                                    <input type="file" name="logo" class="form-control btn-sm">
                                </div>
                            </div>

                            <!-- Favicon del Negocio -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Favicon (Icono Pestaña)</label>
                                <div class="d-flex align-items-center gap-3 bg-light p-2 rounded-3">
                                    @if(isset($config['favicon']) && $config['favicon'])
                                        <img src="{{ $config['favicon'] }}" alt="Favicon" class="img-thumbnail" style="height: 50px;">
                                    @endif
                                    <input type="file" name="favicon" class="form-control btn-sm">
                                </div>
                            </div>

                            <!-- Fondo de Login -->
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Fondo de Pantalla de Login</label>
                                <div class="d-flex align-items-center gap-4 bg-light p-3 rounded-4">
                                    @if(isset($config['imagen_login']) && $config['imagen_login'])
                                        <div class="position-relative">
                                            <img src="{{ $config['imagen_login'] }}" alt="Login BG" class="img-thumbnail shadow-sm" style="max-height: 100px;">
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">Actual</span>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <input type="file" name="imagen_login" class="form-control">
                                        <small class="text-muted d-block mt-1">Sugerido: Imagen horizontal HD (1920x1080). Máx 4MB.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow">
                                <i class="bi bi-save2"></i> GUARDAR CAMBIOS PARA TODO EL SISTEMA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-lg-0 mt-4">
            <div class="card shadow-sm border-0 bg-primary-subtle text-primary-emphasis rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold"><i class="bi bi-info-circle-fill"></i> ¿Sabías que?</h5>
                    <p class="small mb-0">El **nombre del negocio** que pongas aquí aparecerá en el Sidebar, en la pantalla de Login y en la parte superior de cada Ticket que imprimas.</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 bg-info-subtle text-info-emphasis rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold"><i class="bi bi-phone"></i> Tickets Móviles</h5>
                    <p class="small mb-0">Si usas el sistema desde un celular con impresora Bluetooth, el branding se adaptará automáticamente al tamaño del papel térmico de 58mm.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
