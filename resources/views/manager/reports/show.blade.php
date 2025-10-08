@extends('layouts.managerdashboardlayout')

@section('title', $title)
@section('page-title', $title)

@section('content')
<div class="container-fluid mt-4">

    {{-- ---------------- PAGE HEADER ---------------- --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-secondary mb-0">
            <i class="bi bi-bar-chart-line me-2"></i> {{ $title }}
        </h4>
        <a href="{{ route('manager.reports.export', array_merge(['report' => $report], request()->all())) }}" 
           class="btn btn-success btn-sm shadow-sm">
            <i class="bi bi-file-earmark-arrow-down me-1"></i> Export CSV
        </a>
    </div>
    <hr>

    {{-- ---------------- FILTER SECTION ---------------- --}}
    @if(in_array($report, ['active-tenants', 'pending-tenants', 'rejected-tenants', 'maintenance-requests', 'payment-history']))
        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-funnel-fill me-2 text-primary"></i> Filters
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('manager.reports.show', ['report' => $report]) }}" class="row gy-2 gx-3 align-items-center">
                    
                    {{-- Payment History Filters --}}
                    @if($report === 'payment-history')
                        <div class="col-md-3">
                            <label for="payment_for" class="form-label fw-semibold">Purpose</label>
                            <select name="payment_for" id="payment_for" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="rent" {{ request('payment_for') === 'rent' ? 'selected' : '' }}>Rent</option>
                                <option value="utilities" {{ request('payment_for') === 'utilities' ? 'selected' : '' }}>Utilities</option>
                                <option value="others" {{ request('payment_for') === 'others' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>

                    {{-- Maintenance Filters --}}
                    @elseif($report === 'maintenance-requests')
                        <div class="col-md-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="urgency" class="form-label fw-semibold">Urgency</label>
                            <select name="urgency" id="urgency" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="low" {{ request('urgency') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="mid" {{ request('urgency') === 'mid' ? 'selected' : '' }}>Mid</option>
                                <option value="high" {{ request('urgency') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>

                    {{-- Active Tenants Filters --}}
                    @elseif($report === 'active-tenants')
                        <div class="col-md-3">
                            <label for="unit_type" class="form-label fw-semibold">Unit Type</label>
                            <select name="unit_type" id="unit_type" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach(\App\Models\TenantApplication::select('unit_type')->distinct()->get() as $unit)
                                    <option value="{{ $unit->unit_type }}" {{ request('unit_type') === $unit->unit_type ? 'selected' : '' }}>
                                        {{ $unit->unit_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="employment_status" class="form-label fw-semibold">Employment Status</label>
                            <select name="employment_status" id="employment_status" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach(\App\Models\TenantApplication::select('employment_status')->distinct()->get() as $emp)
                                    <option value="{{ $emp->employment_status }}" {{ request('employment_status') === $emp->employment_status ? 'selected' : '' }}>
                                        {{ $emp->employment_status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @endif

    {{-- ---------------- SUMMARY ---------------- --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    @if($report === 'payment-history')
                        <i class="bi bi-cash-coin text-success me-2"></i> Payment Summary
                    @elseif($report === 'maintenance-requests')
                        <i class="bi bi-tools text-warning me-2"></i> Maintenance Summary
                    @else
                        <i class="bi bi-list-ul text-secondary me-2"></i> Summary
                    @endif
                </h5>
                <p class="small text-muted mb-0">
                    @if($report === 'payment-history')
                        Showing: <strong>{{ $currentFilter ? ucfirst($currentFilter) : 'All Categories' }}</strong>
                    @elseif($report === 'maintenance-requests')
                        Status: <strong>{{ request('status') ?: 'All' }}</strong> | 
                        Urgency: <strong>{{ request('urgency') ?: 'All' }}</strong>
                    @elseif($report === 'active-tenants')
                        Unit Type: <strong>{{ request('unit_type') ?: 'All' }}</strong> | 
                        Employment Status: <strong>{{ request('employment_status') ?: 'All' }}</strong>
                    @else
                        Total Records
                    @endif
                </p>
            </div>
            <div class="text-end">
                @if($report === 'payment-history')
                    <span class="small text-muted">Total Paid</span>
                    <div class="fs-4 fw-bold text-success">₱{{ number_format($total ?? 0, 2) }}</div>
                @else
                    <span class="small text-muted">Total</span>
                    <div class="fs-4 fw-bold text-primary">{{ $total ?? $data->total() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ---------------- DATA TABLE ---------------- --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-secondary">
                        {{-- HEADERS --}}
                        @if($report === 'payment-history')
                            <tr>
                                <th>Tenant</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Purpose</th>
                                <th>Status</th>
                            </tr>
                        @elseif(in_array($report, ['active-tenants', 'pending-tenants', 'rejected-tenants']))
                            <tr>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Unit Type</th>
                                <th>Employment Status</th>
                                <th>Source of Income</th>
                                <th>Emergency Contact</th>
                                <th>Status</th>
                            </tr>
                        @elseif($report === 'lease-summary')
                            <tr>
                                <th>Tenant</th>
                                <th>Unit Type</th>
                                <th>Lease Start</th>
                                <th>Lease End</th>
                                <th>Lease Term</th>
                            </tr>
                        @elseif($report === 'maintenance-requests')
                            <tr>
                                <th>Tenant</th>
                                <th>Description</th>
                                <th>Urgency</th>
                                <th>Supposed Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        @endif
                    </thead>

                    <tbody>
                        @forelse($data as $item)
                            {{-- PAYMENT ROWS --}}
                            @if($report === 'payment-history')
                                <tr>
                                    <td>{{ $item->tenant->name ?? 'N/A' }}</td>
                                    <td><span class="fw-semibold">₱{{ number_format($item->pay_amount, 2) }}</span></td>
                                    <td>{{ $item->pay_date?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($item->payment_for) }}</td>
                                    <td>
                                        <form action="{{ route('manager.payments.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="pay_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="Pending" {{ $item->pay_status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Accepted" {{ $item->pay_status === 'Accepted' ? 'selected' : '' }}>Accepted</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>

                            {{-- TENANTS --}}
                            @elseif(in_array($report, ['active-tenants', 'pending-tenants', 'rejected-tenants']))
                                @php $app = $item->tenantApplication; @endphp
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $app->contact_number ?? 'N/A' }}</td>
                                    <td>{{ $app->unit_type ?? 'N/A' }}</td>
                                    <td>{{ $app->employment_status ?? 'N/A' }}</td>
                                    <td>{{ $app->source_of_income ?? 'N/A' }}</td>
                                    <td>{{ $app->emergency_name ?? 'N/A' }} <br><small class="text-muted">{{ $app->emergency_number ?? '' }}</small></td>
                                    <td>
                                        <span class="badge bg-{{ $item->status === 'approved' ? 'success' : ($item->status === 'pending' ? 'warning text-dark' : 'danger') }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>

                            {{-- LEASES --}}
                            @elseif($report === 'lease-summary')
                            @php $lease = $item->leases->first(); @endphp
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->tenantApplication->unit_type ?? 'N/A' }}</td>
                                <td>{{ $lease?->lea_start_date ? \Carbon\Carbon::parse($lease->lea_start_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $lease?->lea_end_date ? \Carbon\Carbon::parse($lease->lea_end_date)->format('M d, Y') : 'N/A' }}</td>
                                <td>{{ $lease?->lea_terms ?? 'N/A' }}</td>
                            </tr>


                            {{-- MAINTENANCE --}}
                            @elseif($report === 'maintenance-requests')
                                <tr>
                                    <td>{{ $item->tenant->name ?? 'N/A' }}</td>
                                    <td>{{ $item->description }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->urgency === 'high' ? 'danger' : ($item->urgency === 'mid' ? 'warning text-dark' : 'secondary') }}">
                                            {{ ucfirst($item->urgency) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->supposed_date)->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('manager.requests.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="Pending" {{ $item->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="In Progress" {{ $item->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Completed" {{ $item->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.requests.show', $item->id) }}" class="btn btn-sm btn-outline-info">View</a>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center text-muted py-3">No records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div class="card-footer bg-light d-flex justify-content-center">
            {{ $data->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
