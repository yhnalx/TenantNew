<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Apartment Portal')</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #333;
        }
        a {
            text-decoration: none;
            transition: color 0.2s ease;
        }
        a:hover {
            color: #0d6efd;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #0d6efd !important;
        }
        header {
            background-color: #ffffff;
            border-bottom: 1px solid #e3e6ea;
        }
        header .btn {
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 6px;
            padding: 6px 14px;
        }
        main {
            padding: 2rem 0;
        }
        footer {
            background-color: #1b1b18;
            color: #f9fafc;
            font-size: 0.9rem;
        }
        footer a {
            color: #f9fafc;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        footer a:hover {
            opacity: 0.8;
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <header class="shadow-sm">
        <div class="container d-flex justify-content-between align-items-center py-3">
            <a class="navbar-brand" href="{{ url('/') }}">TenantMS</a>
            
            @if (Route::has('login'))
            <nav class="d-flex gap-2">
                @auth
                    <a href="{{ route('tenant.home') }}" class="btn btn-outline-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-success">Register</a>
                    @endif
                @endauth
            </nav>
            @endif
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-fill">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto py-4">
        <div class="container text-center">
            <p class="mb-1">&copy; {{ date('Y') }} ApartmentPortal. All rights reserved.</p>
            <p class="mb-0">
                <a href="#">Privacy Policy</a> | 
                <a href="#">Terms & Conditions</a>
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
