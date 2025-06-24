<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Registro de Voluntariado') }}</title>

    <!-- Bootstrap CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
    <style>
        body {
            padding-top: 0;
            background-color: #f8f9fa;
        }
        .app-navbar {
            background-color: #343a40 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .app-navbar .navbar-brand {
            font-weight: bold;
            color: #fff;
        }
        .app-navbar .nav-link {
            color: rgba(255,255,255,.8) !important;
            font-weight: 500;
        }
        .app-navbar .nav-link.active {
            color: #fff !important;
            font-weight: bold;
        }
        
        /* Estilos responsivos para navbar */
        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background-color: #343a40;
                margin-top: 0.5rem;
                padding: 1rem 0;
                border-radius: 0.375rem;
            }
            
            .navbar-nav {
                padding: 0.5rem 0;
            }
            
            .navbar-nav .nav-link {
                padding: 0.75rem 1rem;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            
            .navbar-nav .nav-item:last-child .nav-link {
                border-bottom: none;
            }
            
            .navbar-nav .nav-link:hover {
                background-color: rgba(255,255,255,0.1);
                border-radius: 0.25rem;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            .content-container {
                padding: 15px 0;
            }
        }
        
        /* Estilos para la tabla de registros */
        .table td {
            vertical-align: middle;
        }
        
        .btn-group-vertical .btn {
            margin-bottom: 2px;
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        .badge.bg-light {
            border: 1px solid #dee2e6;
        }
        
        .border-top {
            border-top: 1px solid #dee2e6 !important;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.02);
        }
        
        /* Estilos para modales */
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .modal-lg {
            max-width: 900px;
        }
        
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }
        
        .table-light {
            background-color: #f8f9fa;
        }
        
        /* Estilos para botones en modales */
        .modal .btn-group .btn {
            margin-right: 2px;
        }
        
        .modal .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        .modal .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .content-container {
            padding: 20px 0;
        }
        
        /* Mejoras adicionales para móviles */
        .footer {
            margin-top: auto;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .btn-sm {
                font-size: 0.75rem;
                padding: 0.2rem 0.4rem;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg app-navbar">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Registro de Voluntariado') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="fas fa-home me-1"></i>Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('voluntarios*') ? 'active' : '' }}" href="{{ route('voluntarios.index') }}">
                                <i class="fas fa-users me-1"></i>Voluntarios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('registros*') ? 'active' : '' }}" href="{{ route('registros.index') }}">
                                <i class="fas fa-clipboard-list me-1"></i>Registros
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('formulario*') ? 'active' : '' }}" href="{{ route('registros.formulario') }}">
                                <i class="fas fa-edit me-1"></i>Formulario
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Page Content -->
    <main class="content-container">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">© {{ date('Y') }} Registro de Voluntariado</span>
        </div>
    </footer>
</body>
</html>