@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person-gear text-primary"></i> Editar Usuario</h2>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al listado
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <form action="{{ route('users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre Completo</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre de Usuario (Login)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                           value="{{ old('username', $user->username) }}" required>
                                </div>
                                @error('username') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email (Opcional)</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                                       value="{{ old('telefono', $user->telefono) }}">
                                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Rol en el Sistema</label>
                                <select name="roles[]" class="form-select @error('roles') is-invalid @enderror" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected(in_array($role->id, $userRoles))>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('roles') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Estado del Usuario</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="activo" value="1" @checked($user->activo)>
                                    <label class="form-check-label">Habilitado para ingresar</label>
                                </div>
                            </div>
                            
                            <hr class="mt-4 mb-2">
                            <h5 class="text-primary mt-3"><i class="bi bi-shield-lock-fill"></i> Seguridad</h5>
                            <p class="small text-muted mb-3">Deja las contraseñas en blanco si no deseas cambiarlas.</p>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">Nueva Contraseña</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Escribe la nueva clave aquí si deseas cambiarla">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-pill">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-lg-0 mt-4">
            <div class="card bg-warning-subtle border-warning-subtle rounded-3">
                <div class="card-body">
                    <h5 class="card-title text-warning-emphasis fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Aviso Importante</h5>
                    <p class="card-text text-warning-emphasis small mb-0">
                        <b>Login:</b> Si cambias el `Nombre de Usuario`, el empleado deberá usar el **NUEVO** nombre para entrar al sistema de inmediato.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
