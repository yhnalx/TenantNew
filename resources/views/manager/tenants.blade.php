@extends('layouts.managerdashboardlayout')

@section('title', 'Manage Tenants')

@section('content')
<style>
    body {
        background: #f3f5f9;
        font-family: 'Inter', sans-serif;
    }

    h3.fw-bold {
        background: linear-gradient(90deg, #0d6efd, #00b4d8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Card Styles */
    .tenant-card {
        border: none;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .tenant-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .tenant-card::before {
        content: "";
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 6px;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .tenant-approved::before { background: #198754; }
    .tenant-pending::before { background: #ffc107; }
    .tenant-rejected::before { background: #dc3545; }

    .status-badge {
        border-radius: 50px;
        padding: 5px 14px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-approved { background-color: #d4edda; color: #155724; }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }

    /* Filter Bar */
    .filter-bar {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        padding: 16px 24px;
    }

    .custom-select, .form-control {
        border-radius: 12px !important;
        box-shadow: none !important;
        border: 1.5px solid #dee2e6 !important;
    }

    .custom-select:focus, .form-control:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.25) !important;
    }

    .btn {
        border-radius: 10px !important;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 0;
        opacity: 0.8;
    }

    .empty-state img {
        width: 180px;
        opacity: 0.8;
    }

    /* Modal */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .modal-header {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: white;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
    }

    .modal-footer {
        border-top: none;
        background-color: #f8f9fa;
    }

    .modal .btn-close {
        filter: invert(1);
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Tenant Management</h3>
    </div>

    <!-- Filter + Search -->
    <div class="filter-bar mb-4">
        <form method="GET" action="{{ route('manager.tenants') }}" class="d-flex flex-wrap align-items-center gap-3 mb-0">
            <!-- Filter & Export Section -->
            <div class="d-flex flex-wrap align-items-center gap-3 bg-white p-3 rounded-4 shadow-sm border w-100">

                <!-- Filter by Status -->
                <div class="d-flex align-items-center gap-2">
                    <label for="filter" class="fw-semibold text-secondary mb-0">
                        <i class="bi bi-funnel-fill text-primary me-1"></i> Status
                    </label>
                    <div class="position-relative">
                        <select name="filter" id="filter"
                            class="form-select form-select-sm rounded-pill ps-3 pe-5 shadow-sm border-primary-subtle"
                            style="min-width: 180px;" onchange="this.form.submit()">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Tenants</option>
                            <option value="pending" {{ $filter === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ $filter === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ $filter === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <i class="bi bi-chevron-down position-absolute top-50 end-0 translate-middle-y me-3 text-muted small"></i>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <label for="search" class="fw-semibold text-secondary mb-0">
                        <i class="bi bi-search text-primary me-1"></i> Tenant
                    </label>
                    <div class="input-group input-group-sm" style="max-width: 300px;">
                        <input type="text" name="search" id="search"
                            class="form-control rounded-start-pill border-end-0 shadow-sm"
                            placeholder="Search tenant name..." value="{{ $search }}">
                        <button class="btn btn-primary rounded-end-pill px-3">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Export PDF Button -->
                <div class="ms-auto">
                    <a href="{{ route('manager.tenants.export', ['filter' => $filter, 'search' => $search]) }}"
                        target="_blank"
                        class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 rounded-pill shadow-sm px-3 py-2">
                        <i class="bi bi-file-earmark-pdf-fill fs-6"></i>
                        <span class="fw-semibold">Export PDF</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <style>
        /* Improved dropdown icon positioning */
        .dropdown-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6c757d;
        }

        /* Responsive filter bar */
        @media (max-width: 768px) {
            .filter-bar form > div {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .filter-bar .input-group {
                width: 100% !important;
            }

            .filter-bar .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>


    <!-- Tenant Cards -->
    <div class="row g-4">
        @forelse($filteredTenants as $tenant)
            <div class="col-md-4">
                <div class="card tenant-card tenant-{{ $tenant->status }} p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-semibold text-dark mb-0">{{ $tenant->name }}</h5>
                        <span class="status-badge status-{{ $tenant->status }}">
                            {{ ucfirst($tenant->status) }}
                        </span>
                    </div>

                    @if($tenant->status !== 'rejected')
                        <p class="mb-1"><strong>Email:</strong> {{ $tenant->email }}</p>
                        <p class="mb-1"><strong>Rent Balance:</strong> ₱{{ number_format($tenant->rent_balance, 2) }}</p>
                        <p class="mb-3"><strong>Utility Balance:</strong> ₱{{ number_format($tenant->utility_balance, 2) }}</p>
                    @else
                        <p class="mt-3 mb-3 text-danger fw-semibold text-center">
                            Rejected Tenant (Reason: {{ $tenant->rejection_reason ?? 'N/A' }})
                        </p>
                    @endif

                    <div class="d-flex justify-content-between mt-3">
                        @if($tenant->tenantApplication && $tenant->tenantApplication->valid_id_path && $tenant->tenantApplication->id_picture_path)
                            <a href="{{ route('manager.tenants.viewIds', $tenant->id) }}" target="_blank"
                               class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
                                <i class="bi bi-eye"></i> View IDs
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="bi bi-eye-slash"></i> No IDs
                            </button>
                        @endif

                        @if($tenant->status === 'approved')
                            <form action="{{ route('manager.tenants.notify', $tenant->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                                    <i class="bi bi-envelope-fill"></i> Notify
                                </button>
                            </form>
                        @elseif($tenant->status === 'pending')
                            <div class="d-flex gap-2">
                                <form action="{{ route('manager.tenant.approve', $tenant->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectTenantModal{{ $tenant->id }}">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076504.png" alt="No tenants">
                <p class="text-muted mt-3">No tenants found for this filter.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Reject Modals -->
@foreach($filteredTenants as $tenant)
    @if($tenant->status === 'pending')
        <div class="modal fade" id="rejectTenantModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="{{ route('manager.tenant.reject') }}">
                    @csrf
                    <input type="hidden" name="tenant_id" value="{{ $tenant->id }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-x-circle"></i> Reject Tenant</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Please provide a reason for rejecting <strong>{{ $tenant->name }}</strong>:</p>
                            <div class="mb-3">
                                <label class="form-label">Reason</label>
                                <select name="rejection_reason" class="form-select" required>
                                    <option value="">Select reason...</option>
                                    <option value="Incomplete application">Incomplete application</option>
                                    <option value="Failed background check">Failed background check</option>
                                    <option value="Credit score too low">Credit score too low</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Confirm Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach
@endsection
