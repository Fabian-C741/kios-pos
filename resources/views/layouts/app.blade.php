<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Kiosco POS') }} - @yield('title', 'Inicio')</title>

    @php
        $brandingFavicon = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'favicon')->value('value');
    @endphp
    @if($brandingFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $brandingFavicon }}">
    @endif

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS (Bootstrap & Icons) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --dark-bg: #1a1c23;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
            overflow-x: hidden;
        }

        #wrapper {
            display: flex;
            width: 100%;
        }

        #sidebar-wrapper {
            min-height: 100vh;
            width: var(--sidebar-width);
            background-color: #4e73df;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-heading {
            padding: 1.5rem 1rem;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .list-group-item {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, .8);
            padding: 0.75rem 1.25rem;
            border-radius: 0;
            transition: all 0.2s;
        }

        .list-group-item:hover, .list-group-item.active {
            background-color: rgba(255, 255, 255, .1);
            color: #fff;
        }

        .list-group-item i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }

        #page-content-wrapper {
            width: 100%;
            min-height: 100vh;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .badge-stock {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Estilos específicos para Kiosco */
        .card-stat {
            border: none;
            border-radius: 10px;
            transition: transform 0.2s;
            overflow: hidden;
        }

        .card-stat:hover {
            transform: translateY(-5px);
        }

        @media (max-width: 768px) {
            #sidebar-wrapper {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #wrapper.toggled #sidebar-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('css')
</head>
<body>
    @php
        $brandingName = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'nombre_kiosco')->value('value') ?? 'Kiosco POS';
        $brandingLogo = \Illuminate\Support\Facades\DB::table('configuraciones')->where('key', 'logo')->value('value');
    @endphp

    <div id="wrapper">
        @auth
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom text-center">
                @if($brandingLogo)
                    <img src="{{ $brandingLogo }}" alt="Logo" class="img-fluid mb-2" style="max-height: 40px;">
                @else
                    <i class="bi bi-shop"></i>
                @endif
                {{ $brandingName }}
            </div>
            <div class="list-group list-group-flush mt-3">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                
                <a href="{{ route('ventas.create') }}" class="list-group-item list-group-item-action {{ request()->routeIs('ventas.create') ? 'active' : '' }}">
                    <i class="bi bi-cart-plus"></i> Nueva Venta
                </a>

                <a href="{{ route('ventas.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('ventas.index') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Historial Ventas
                </a>

                <a href="{{ route('productos.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('productos.index') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Inventario
                </a>

                <a href="{{ route('productos.stock-bajo') }}" class="list-group-item list-group-item-action {{ request()->routeIs('productos.stock-bajo') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle-fill text-warning"></i> Faltantes (WhatsApp)
                </a>

                @if(auth()->user()->isAdmin())
                <div class="small fw-bold text-uppercase px-3 mt-4 mb-2 opacity-50">Administración</div>
                
                <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Usuarios
                </a>
                
                <a href="{{ route('configuracion.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
                    <i class="bi bi-gear"></i> Configuración
                </a>
                @endif

                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="list-group-item list-group-item-action mt-4 text-warning">
                        <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                    </a>
                </form>
            </div>
        </div>
        @endauth

        <!-- Page Content -->
        <div id="page-content-wrapper">
            @auth
            <nav class="navbar navbar-expand-lg navbar-light border-bottom py-3">
                <div class="container-fluid">
                    <button class="btn btn-outline-primary d-md-none" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <!-- Indicador Offline/PWA -->
                        <div id="offline-indicator" class="me-3 badge bg-success- Greenwich text-success border border-success-subtle rounded-pill px-3">
                            <i class="bi bi-wifi"></i> Online
                        </div>

                        <div class="dropdown">
                            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-4 me-2"></i>
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" aria-labelledby="dropdownUser">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="bi bi-box-arrow-left me-2"></i> Salir
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            @endauth

            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ELIMINAR CUALQUER SERVICE WORKER ANTIGUO (Causante del 404 y bloqueo de cache)
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.unregister();
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    document.getElementById('wrapper').classList.toggle('toggled');
                });
            }
        });
    </script>
    @stack('js')
</body>
</html>
