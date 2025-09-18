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

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Units</h5>
                <p class="card-text fs-3">{{ $totalUnits }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Occupied Units</h5>
                <p class="card-text fs-3">{{ $occupiedUnits }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Vacant Units</h5>
                <p class="card-text fs-3">{{ $vacantUnits }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Pending Applications</h5>
                <p class="card-text fs-3">{{ $pendingApplications }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Pie Chart --}}
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">
        Tenant Status Distribution
    </div>
    <div class="card-body">
        <div style="max-width: 400px; margin:auto;">
            <canvas id="tenantStatusChart"></canvas>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('tenantStatusChart').getContext('2d');
    const tenantStatusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                label: 'Tenant Status',
                data: [{{ $approvedTenants }}, {{ $pendingApplications }}, {{ $rejectedTenants }}],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
        }
    });
</script>
@endsection
