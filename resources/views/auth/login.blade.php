@extends('layouts.app')

@section('content')
@php
    $brandingName = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'nombre_kiosco')->value('value') ?? 'Kiosco POS';
    $brandingLogo = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'logo')->value('value');
    $brandingBG = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'imagen_login')->value('value');
@endphp

<div class="login-page-wrapper" style="
    @if($brandingBG)
        background-image: url('{{ $brandingBG }}');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    @else
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    @endif
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: -1.5rem; /* Compensar padding de container fluido */
">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden" 
                     style="backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9);">
                    <div class="card-header bg-primary text-white text-center py-4 border-0">
                        @if($brandingLogo)
                            <img src="{{ $brandingLogo }}" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                        @else
                            <i class="bi bi-shop fs-1 mb-2 d-block"></i>
                        @endif
                        <h4 class="mb-0 fw-bold">{{ $brandingName }}</h4>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">USUARIO O CORREO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="login" class="form-control bg-light border-0 @error('login') is-invalid @enderror" 
                                           value="{{ old('login') }}" required autofocus placeholder="Ingresá tu usuario">
                                </div>
                                @error('login')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted">CONTRASEÑA</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" 
                                           required placeholder="••••••••">
                                </div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label small text-muted" for="remember">Recordar mi sesión</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-box-arrow-in-right"></i> INGRESAR AL SISTEMA
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Ocultar elementos de layout en login */
    #sidebar-wrapper, nav.navbar, .alert { display: none !important; }
    #page-content-wrapper { padding-left: 0 !important; width: 100% !important; }
    .container-fluid { padding: 0 !important; }
</style>
@endsection
