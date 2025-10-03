@extends('layouts.tenantdashboardlayout')

@section('title', 'Tenant Dashboard')

@section('content')
<div class="container-fluid pt-4">

    {{-- Welcome --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold">Welcome, {{ Auth::user()->name }} 👋</h2>
        <p class="text-muted">Here’s your tenant dashboard overview</p>
    </div>

    @php
        $user = Auth::user();
        $payments = $payments ?? collect();
        $requests = $requests ?? collect();
    @endphp

    {{-- Tenant Info --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">Your Information</div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Status:</strong>
                @if($user->status === 'approved')
                    <span class="badge bg-success">Approved ✅</span>
                @elseif($user->status === 'pending')
                    <span class="badge bg-warning text-dark">Pending ⏳</span>
                @else
                    <span class="badge bg-danger">Rejected ❌</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Payments</h5>
                    <p class="card-text fs-3">₱{{ number_format($payments->sum('amount'), 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending Requests</h5>
                    <p class="card-text fs-3">{{ $requests->where('status','pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-secondary shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Requests</h5>
                    <p class="card-text fs-3">{{ $requests->count() }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
