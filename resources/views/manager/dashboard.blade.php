@extends('layouts.managerdashboardlayout')

@section('title', 'Overview')
@section('page-title', 'Overview')

@section('content')
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row g-4 mb-4">
    <!-- Total Units -->
    <div class="col-md-3">
        <div class="card shadow-lg border-0 rounded-4 bg-gradient-primary text-white h-100">
            <div class="card-body text-center">
                <i class="bi bi-building-check fs-1 mb-2"></i>
                <h6 class="fw-bold">Total Units</h6>
                <p class="fs-3 fw-semibold mb-0">{{ $totalUnits }}</p>
            </div>
        </div>
    </div>

    <!-- Occupied Units -->
    <div class="col-md-3">
        <div class="card shadow-lg border-0 rounded-4 bg-gradient-success text-white h-100">
            <div class="card-body text-center">
                <i class="bi bi-house-door-fill fs-1 mb-2"></i>
                <h6 class="fw-bold">Occupied Units</h6>
                <p class="fs-3 fw-semibold mb-0">{{ $occupiedUnits }}</p>
            </div>
        </div>
    </div>

    <!-- Vacant Units -->
    <div class="col-md-3">
        <div class="card shadow-lg border-0 rounded-4 bg-gradient-warning text-dark h-100">
            <div class="card-body text-center">
                <i class="bi bi-door-closed-fill fs-1 mb-2"></i>
                <h6 class="fw-bold">Vacant Units</h6>
                <p class="fs-3 fw-semibold mb-0">{{ $vacantUnits }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Applications -->
    <div class="col-md-3">
        <div class="card shadow-lg border-0 rounded-4 bg-gradient-info text-white h-100">
            <div class="card-body text-center">
                <i class="bi bi-clipboard-check-fill fs-1 mb-2"></i>
                <h6 class="fw-bold">Pending Applications</h6>
                <p class="fs-3 fw-semibold mb-0">{{ $pendingApplications }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Pie Chart --}}
<div class="card shadow-lg border-0 rounded-4 mb-4">
    <div class="card-header text-white fw-bold rounded-top-4" 
         style="background: linear-gradient(135deg, #6c757d, #495057);">
        <i class="bi bi-pie-chart-fill me-2"></i> Tenant Status Distribution
    </div>
    <div class="card-body">
        <div style="max-width: 450px; margin:auto;">
            <canvas id="tenantStatusChart"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('tenantStatusChart').getContext('2d');
    const tenantStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                label: 'Tenant Status',
                data: [{{ $approvedTenants }}, {{ $pendingApplications }}, {{ $rejectedTenants }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 2,
                hoverOffset: 10,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
</script>

{{-- Extra styling for gradients --}}
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #198754, #157347);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #ffcd39);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #0dcaf0, #0aa2c0);
    }

    .card:hover {
        transform: translateY(-4px);
        transition: 0.3s ease;
    }
</style>
@endsection
