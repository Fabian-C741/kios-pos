<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d6efd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Kiosco">
    <link rel="manifest" href="/manifest.json">
    <title>{{ $title ?? 'Sistema Kiosco' }} - @yield('title', '')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0d6efd;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
        }
        .sidebar {
            min-height: 100vh;
            background: #1a1d21;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            border-radius: 0.375rem;
            margin: 2px 8px;
        }
        .sidebar .nav-link:hover {
            background: #2d3238;
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: var(--primary);
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 24px;
        }
        .card-stat {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .badge-stock {
            font-size: 0.75rem;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            @auth
            <nav class="col-md-2 d-md-block sidebar collapse show" id="sidebarMenu">
                <div class="position-sticky pt-3">
                    <div class="px-3 mb-4">
                        <h5 class="text-white mb-0">
                            <i class="bi bi-shop"></i> Kiosco
                        </h5>
                        <small class="text-muted">Sistema de Ventas</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}" href="{{ route('ventas.index') }}">
                                <i class="bi bi-cart3"></i> Ventas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" href="{{ route('productos.index') }}">
                                <i class="bi bi-box-seam"></i> Productos
                            </a>
                        </li>
                        @role('admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-people"></i> Usuarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('cajas.*') ? 'active' : '' }}" href="{{ route('cajas.index') }}">
                                <i class="bi bi-cash-register"></i> Cajas
                            </a>
                        </li>
                        @endrole
                        <li class="nav-item mt-3">
                            <a class="nav-link {{ request()->routeIs('productos.stock-bajo') ? 'active' : '' }}" href="{{ route('productos.stock-bajo') }}">
                                <i class="bi bi-exclamation-triangle"></i> Stock Bajo
                                @php
                                    $stockBajoCount = \App\Models\Producto::stockBajo()->where('activo', true)->count();
                                @endphp
                                @if($stockBajoCount > 0)
                                    <span class="badge bg-danger float-end">{{ $stockBajoCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ventas.reporte') ? 'active' : '' }}" href="{{ route('ventas.reporte') }}">
                                <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="my-3 border-secondary">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-gear"></i> Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link border-0 w-100 text-start bg-transparent">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-md-4">
                <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
                    <div class="container-fluid">
                        <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <i class="bi bi-list"></i>
                        </button>
                        <span class="navbar-text">
                            <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        </span>
                    </div>
                </nav>
            @else
            <main class="col-12">
            @endauth
            
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-x-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW registered'))
                    .catch(err => console.log('SW registration failed'));
            });
        }
    </script>
</body>
</html>
