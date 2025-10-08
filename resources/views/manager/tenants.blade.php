@extends('layouts.managerdashboardlayout')

@section('title', 'Manage Tenants')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .card {
        border-radius: 16px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        border: none;
    }
    .card-header {
        font-weight: 600;
        font-size: 1rem;
        border: none;
    }
    .card-header.bg-primary { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
    .card-header.bg-success { background: linear-gradient(135deg, #198754, #157347); }
    .card-header.bg-danger { background: linear-gradient(135deg, #dc3545, #b02a37); }

    .table thead th {
        text-align: center;
        vertical-align: middle;
        font-weight: 600;
        background: #f8f9fa;
    }
    .table tbody tr:hover {
        background: #f1f5ff;
        transition: 0.2s ease-in-out;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.45em 0.8em;
        border-radius: 8px;
    }
    .btn {
        border-radius: 8px;
    }
    .btn-sm {
        padding: 0.35rem 0.7rem;
        font-size: 0.85rem;
    }

    /* Modal */
    .modal-content {
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        border: none;
    }
    .modal-header {
        background: linear-gradient(135deg, #0d6efd, #0a58ca);
        color: #fff;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
    }
    .modal-footer {
        border-top: none;
    }
    .modal .btn-close {
        filter: invert(1);
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold text-primary">
        <i class="bi bi-people-fill"></i> Tenant Management
    </h2>

    <!-- Pending Tenants -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-hourglass-split me-1"></i> Pending Tenant Applications
        </div>
        <div class="card-body">
            @if($pendingTenants->isEmpty())
                <p class="text-muted text-center">No pending applications.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingTenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->email }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    </td>
                                    <td>
                                        <!-- Approve -->
                                        <form action="{{ route('manager.tenant.approve', $tenant->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check-circle"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Reject -->
                                        <button type="button" class="btn btn-danger btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectTenantModal{{ $tenant->id }}">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Approved Tenants -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <i class="bi bi-check2-circle me-1"></i> Approved Tenants
        </div>
        <div class="card-body">
            @if($approvedTenantList->isEmpty())
                <p class="text-muted text-center">No approved tenants.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedTenantList as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->email }}</td>
                                    <td><span class="badge bg-success">Approved</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Rejected Tenants -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <i class="bi bi-x-octagon me-1"></i> Rejected Tenants
        </div>
        <div class="card-body">
            @if($rejectedTenantList->isEmpty())
                <p class="text-muted text-center">No rejected tenants.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rejectedTenantList as $tenant)
                                <tr>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->email }}</td>
                                    <td><span class="badge bg-danger">Rejected</span></td>
                                    <td>{{ $tenant->rejection_reason ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modals -->
@foreach($pendingTenants as $tenant)
<div class="modal fade" id="rejectTenantModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
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
@endforeach
@endsection
