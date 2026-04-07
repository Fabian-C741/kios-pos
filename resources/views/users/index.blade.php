@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="bi bi-people"></i> Usuarios del Sistema</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase small">
                        <tr>
                            <th class="px-4 py-3">Nombre</th>
                            <th class="py-3">Usuario (Login)</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Rol</th>
                            <th class="py-3 text-center">Estado</th>
                            <th class="px-4 py-3 text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $user->name }}</div>
                                        <div class="small text-muted">{{ $user->telefono ?? 'Sin teléfono' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="bg-light px-2 py-1 rounded text-primary">{{ $user->username }}</code>
                            </td>
                            <td>
                                @if($user->email)
                                    {{ $user->email }}
                                @else
                                    <span class="text-muted small">No registrado</span>
                                @endif
                            </td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge {{ $role->name === 'admin' ? 'bg-danger' : 'bg-info' }} rounded-pill px-2">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                @if($user->activo)
                                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="bi bi-check-circle me-1"></i> Activo</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="bi bi-x-circle me-1"></i> Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 text-end">
                                <div class="btn-group btn-group-sm mb-0">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No hay usuarios registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 bg-light border-top">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
