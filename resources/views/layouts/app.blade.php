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
            background-color: #f9fafc;
        }
        a {
            text-decoration: none;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        footer {
            background-color: #1b1b18;
            color: #f9fafc;
        }
        footer a {
            color: #f9fafc;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Header / Navbar -->
    <header class="bg-white shadow-sm mb-4">
        <div class="container d-flex justify-content-between align-items-center py-3">
            <a class="navbar-brand text-primary" href="{{ url('/') }}">TenantMS</a>
            
            @if (Route::has('login'))
            <nav class="d-flex gap-3">
                @auth
                    <a href="{{ route('tenant.home') }}" class="btn btn-outline-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-success btn-sm">Register</a>
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
