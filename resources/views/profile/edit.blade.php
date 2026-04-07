@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-circle text-primary"></i> Mi Perfil de Usuario</h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">Información Personal y Logueo</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre Completo</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre de Usuario (Username)</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
                                </div>
                                @error('username') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email (Opcional)</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $user->telefono) }}">
                                @error('telefono') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambio de Contraseña -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">Seguridad y Contraseña</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Contraseña Actual</label>
                                <input type="password" name="current_password" class="form-control" required>
                                @error('current_password') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="Escribe tu nueva clave aquí" required>
                                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-dark px-4 py-2 rounded-pill">
                                <i class="bi bi-key"></i> Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-lg-0 mt-4">
            <div class="card shadow-sm border-0 bg-info-subtle text-info-emphasis rounded-3">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="bi bi-info-circle"></i> Tips de Acceso</h5>
                    <p class="small mb-0">Recordá que ahora tu **Nombre de Usuario** es lo que necesitás para entrar al sistema. Si lo cambiás acá, usalo la próxima vez que inicies sesión.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
