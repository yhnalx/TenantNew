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
        // Ensure $payments and $requests are collections
        $payments = $payments ?? collect();
        $requests = $requests ?? collect();
    @endphp

    {{-- Tenant Info --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">Your Information</div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p><strong>Status:</strong>
                @if(Auth::user()->status === 'approved')
                    <span class="badge bg-success">Approved ✅</span>
                @elseif(Auth::user()->status === 'pending')
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

    {{-- Payment History --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">Payment History</div>
        <div class="card-body">
            @if($payments->isEmpty())
                <p>No payments found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                    <td>{{ ucfirst($payment->type) }}</td>
                                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Future Features --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light">Maintenance Requests</div>
                <div class="card-body">
                    <p>You can request maintenance here soon.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light">Notifications</div>
                <div class="card-body">
                    <p>Any updates or announcements will appear here.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
