<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Registro de Voluntariado') }}</title>

    <!-- Bootstrap CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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
        .content-container {
            padding: 20px 0;
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
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('voluntarios*') ? 'active' : '' }}" href="{{ route('voluntarios.index') }}">Voluntarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('registros*') ? 'active' : '' }}" href="{{ route('registros.index') }}">Registros</a>
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