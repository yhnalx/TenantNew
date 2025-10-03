<!-- resources/views/layouts/managerdashboardlayout.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - @yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Bootstrap Bundle JS (with Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/managerdashboard.css') }}">
</head>
<body>

<div class="d-flex" id="dashboardWrapper">
    <!-- Sidebar -->
    @include('layouts.sidebarlayout')

    <!-- Main Content -->
    <main class="flex-grow-1 ms-lg-250 px-4 py-4" style="margin-left: 250px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">@yield('page-title')</h2>
        </div>
        @yield('content')
    </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
