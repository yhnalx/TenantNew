<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tenant Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Optional: Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/tenantdashboard.css') }}">
</head>
<body>
<div class="d-flex" id="dashboardWrapper">

    {{-- Sidebar --}}
    <nav class="bg-primary text-white vh-100 p-3 position-fixed" style="width: 250px;">
        <div class="text-center mb-4">
            <h4 class="fw-bold">🏠 Tenant</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->routeIs('tenant.home') ? 'active bg-success text-primary rounded' : '' }}" 
                   href="{{ route('tenant.home') }}">
                    <i class="bi bi-house-fill me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->routeIs('tenant.payments') ? 'active bg-success text-primary rounded' : '' }}" 
                   href="{{ route('tenant.payments') }}">
                    <i class="bi bi-cash-stack me-2"></i> Payments
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white {{ request()->routeIs('tenant.requests') ? 'active bg-success text-primary rounded' : '' }}" 
                   href="{{ route('tenant.requests') }}">
                    <i class="bi bi-tools me-2"></i> Maintenance Requests
                </a>
            </li>
            <li class="nav-item mt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>

    {{-- Main Content --}}
    <main class="flex-grow-1 ms-250 p-4" style="margin-left: 250px;">
        @yield('content')
    </main>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
