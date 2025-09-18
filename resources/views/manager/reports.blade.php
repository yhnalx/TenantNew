@extends('layouts.managerdashboardlayout')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="row mb-4">

    <div class="row mb-4">
        <div class="col-md-4">
            <form method="GET" action="{{ route('manager.reports') }}">
                <label for="month" class="form-label">Select Month:</label>
                <input type="month" id="month" name="month" class="form-control" value="{{ request('month') }}">
                <button type="submit" class="btn btn-primary mt-2">Filter</button>
            </form>
        </div>
    </div>

    
    <!-- Total Tenant Payment -->
    <div class="col-md-6">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Tenant Payment</h5>
                <p class="card-text fs-3">₱120,000</p> <!-- Hardcoded for now -->
            </div>
        </div>
    </div>

    <!-- Total Utilities Payment -->
    <div class="col-md-6">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Utilities Payment</h5>
                <p class="card-text fs-3">₱35,000</p> <!-- Hardcoded for now -->
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Tenant Status Distribution
            </div>
            <div class="card-body d-flex justify-content-center">
                <div style="max-width: 300px; width: 100%;">
                    <canvas id="tenantStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                Occupancy Report
            </div>
            <div class="card-body d-flex justify-content-center">
                <div style="max-width: 300px; width: 100%;">
                    <canvas id="occupancyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Tenant Status Pie Chart
    const ctxStatus = document.getElementById('tenantStatusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'pie',
        data: {
            labels: ['Approved', 'Pending', 'Rejected'],
            datasets: [{
                data: [30, 5, 2], // Example values
                backgroundColor: ['#198754', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Occupancy Bar Chart
    const ctxOccupancy = document.getElementById('occupancyChart').getContext('2d');
    new Chart(ctxOccupancy, {
        type: 'bar',
        data: {
            labels: ['Total Units', 'Occupied Units', 'Vacant Units'],
            datasets: [{
                label: 'Units',
                data: [50, 35, 15], // Example values
                backgroundColor: ['#0d6efd', '#198754', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
