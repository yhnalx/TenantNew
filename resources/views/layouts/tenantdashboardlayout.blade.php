@php
use App\Models\Payment;

$hasPaidDeposit = Payment::where('tenant_id', auth()->id())
    ->get()
    ->contains(function ($payment) {
        return strtolower($payment->payment_for) === 'deposit';
    });
@endphp



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

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            border-radius: 0 15px 15px 0;
            box-shadow: 2px 0 15px rgba(0, 0, 0, 0.15);
        }

        .sidebar .nav-link {
            color: #f1f1f1;
            border-radius: 12px;
            padding: 10px 15px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: #ffffff;
            color: #007bff !important;
            font-weight: 600;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        /* Disabled menu style */
        .nav-link.disabled {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
</head>
<body>
<div class="d-flex" id="dashboardWrapper">

    {{-- Sidebar --}}
    <nav class="sidebar vh-100 p-3 position-fixed">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Tenant</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tenant.home') ? 'active' : '' }}" 
                   href="{{ route('tenant.home') }}">
                    <i class="bi bi-house-fill me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('tenant.payments') ? 'active' : '' }}" 
                   href="{{ route('tenant.payments') }}">
                    <i class="bi bi-cash-stack me-2"></i> Payments
                </a>
            </li>

            {{-- Maintenance Requests (only visible if deposit paid) --}}
            @if($hasPaidDeposit)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.requests') ? 'active' : '' }}" 
                       href="{{ route('tenant.requests') }}">
                        <i class="bi bi-tools me-2"></i> Maintenance Requests
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link disabled" href="#" 
                       title="Complete your deposit payment to access Maintenance Requests">
                        <i class="bi bi-tools me-2"></i> Maintenance Requests
                    </a>
                </li>
            @endif

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
    <main class="main-content flex-grow-1">
        @yield('content')
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
