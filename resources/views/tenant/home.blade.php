@extends('layouts.tenantdashboardlayout')

@section('title', 'Tenant Dashboard')

<style>
    /* General */
    body {
        background: #f4f7fb;
        font-family: 'Inter', sans-serif;
    }

    h2, h3, h5, h6 {
        font-weight: 600;
    }

    /* Welcome Section */
    .welcome-title {
        font-size: 2.2rem;
        font-weight: 700;
        background: linear-gradient(135deg, #0061f2, #4e9cff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .welcome-subtitle {
        font-size: 1.1rem;
        color: #6c757d;
    }

    /* Cards (Glassmorphism Style) */
    .card-modern {
        border: none;
        border-radius: 1.2rem;
        backdrop-filter: blur(12px);
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.12);
    }

    .card-modern .card-header {
        border-radius: 1.2rem 1.2rem 0 0;
        background: linear-gradient(135deg, #0061f2, #4e9cff);
        font-weight: 600;
        font-size: 1.1rem;
    }

    /* Summary Cards */
    .summary-card {
        border: none;
        border-radius: 1rem;
        padding: 2rem 1.5rem;
        color: #fff;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .summary-card h6 {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    .summary-card h3 {
        font-size: 1.8rem;
        font-weight: 700;
    }
    .summary-card:hover {
        transform: translateY(-6px);
        filter: brightness(1.05);
    }

    .summary-payments {
        background: linear-gradient(135deg, #17a2b8, #63d6e5);
    }
    .summary-pending {
        background: linear-gradient(135deg, #ffc107, #ffda6a);
        color: #333;
    }
    .summary-total {
        background: linear-gradient(135deg, #6c757d, #adb5bd);
    }

    /* Badges */
    .badge-modern {
        padding: 0.45rem 0.9rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 500;
    }
</style>

@section('content')
<div class="container-fluid pt-4">

    {{-- Welcome --}}
    <div class="text-center mb-5">
        <h2 class="welcome-title">Welcome, {{ Auth::user()->name }} 👋</h2>
        <p class="welcome-subtitle">Here’s your tenant dashboard overview</p>
    </div>

    @php
        $user = Auth::user();
        $lease = $lease ?? null;
        $application = $application ?? null;
        $payments = $payments ?? collect();
        $requests = $requests ?? collect();
    @endphp

    {{-- Tenant Info --}}
    <div class="card shadow-lg border-0 rounded-4 mb-5">
        <div class="card-header text-white fw-bold rounded-top-4" 
            style="background: linear-gradient(135deg, #007bff, #0056b3);">
            <i class="bi bi-person-badge-fill me-2"></i> Your Information
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-circle text-primary fs-5 me-2"></i>
                        <p class="mb-0"><strong>Name:</strong> {{ $user->name }}</p>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-envelope-fill text-primary fs-5 me-2"></i>
                        <p class="mb-0"><strong>Email:</strong> {{ $user->email }}</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-primary fs-5 me-2"></i>
                        <p class="mb-0"><strong>Status:</strong>
                            @if($user->status === 'approved')
                                <span class="badge rounded-pill bg-success px-3 py-2">Approved ✅</span>
                            @elseif($user->status === 'pending')
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2">Pending ⏳</span>
                            @else
                                <span class="badge rounded-pill bg-danger px-3 py-2">Rejected ❌</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    @if($lease)
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-building text-primary fs-5 me-2"></i>
                            <p class="mb-0"><strong>Unit (Room No):</strong> {{ $lease->room_no ?? 'N/A' }}</p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-calendar-event text-primary fs-5 me-2"></i>
                            <p class="mb-0"><strong>Lease Start:</strong> 
                                {{ optional($lease->lea_start_date)->format('M d, Y') ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-calendar-check text-primary fs-5 me-2"></i>
                            <p class="mb-0"><strong>Lease End:</strong> 
                                {{ optional($lease->lea_end_date)->format('M d, Y') ?? 'N/A' }}
                            </p>
                        </div>
                    @endif

                    @if($application)
                        <div class="d-flex align-items-center">
                            <i class="bi bi-door-open-fill text-primary fs-5 me-2"></i>
                            <p class="mb-0"><strong>Unit Type:</strong> {{ $application->unit_type ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>



    {{-- Summary Cards --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="summary-card summary-payments text-center">
                <h6>Total Payments</h6>
                <h3>₱{{ number_format($payments->sum('pay_amount'), 2) }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card summary-pending text-center">
                <h6>Pending Requests</h6>
                <h3>{{ $requests->where('status','Pending')->count() }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="summary-card summary-total text-center">
                <h6>Total Requests</h6>
                <h3>{{ $requests->count() }}</h3>
            </div>
        </div>
    </div>

</div>
@endsection
