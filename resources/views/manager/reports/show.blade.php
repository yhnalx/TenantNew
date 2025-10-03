@extends('layouts.managerdashboardlayout')

@section('title', $title)
@section('page-title', $title)

@section('content')
<div class="container mt-4">
    
    <h4>{{ $title }}</h4>
    <hr>

    {{-- ---------------- FILTER & EXPORT ---------------- --}}
    @if(in_array($report, ['active-tenants', 'pending-tenants', 'rejected-tenants', 'maintenance-requests', 'payment-history']))
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('manager.reports.show', ['report' => $report]) }}" class="row g-2 align-items-center">
                    
                    @if($report === 'payment-history')
                        <div class="col-auto">
                            <label for="payment_for" class="col-form-label fw-bold">Filter by Purpose:</label>
                        </div>
                        <div class="col-auto">
                            <select name="payment_for" id="payment_for" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="rent" {{ request('payment_for') === 'rent' ? 'selected' : '' }}>Rent</option>
                                <option value="utilities" {{ request('payment_for') === 'utilities' ? 'selected' : '' }}>Utilities</option>
                                <option value="others" {{ request('payment_for') === 'others' ? 'selected' : '' }}>Others</option>
                            </select>
                        </div>
                    @elseif($report === 'maintenance-requests')
                        <div class="col-auto">
                            <label for="status" class="col-form-label fw-bold">Filter by Status:</label>
                        </div>
                        <div class="col-auto">
                            <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ request('status') === 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="urgency" class="col-form-label fw-bold">Filter by Urgency:</label>
                        </div>
                        <div class="col-auto">
                            <select name="urgency" id="urgency" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                <option value="low" {{ request('urgency') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="mid" {{ request('urgency') === 'mid' ? 'selected' : '' }}>Mid</option>
                                <option value="high" {{ request('urgency') === 'high' ? 'selected' : '' }}>High</option>
                            </select>
                        </div>
                    @elseif($report === 'active-tenants')
                        <div class="col-auto">
                            <label for="unit_type" class="col-form-label fw-bold">Filter by Unit Type:</label>
                        </div>
                        <div class="col-auto">
                            <select name="unit_type" id="unit_type" class="form-select" onchange="this.form.submit()">
                                <option value="">All</option>
                                @foreach(\App\Models\TenantApplication::select('unit_type')->distinct()->get() as $unit)
                                    <option value="{{ $unit->unit_type }}" {{ request('unit_type') === $unit->unit_type ? 'selected' : '' }}>
                                        {{ $unit->unit_type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto">
                            <label for="employment_status" class="col-form-label fw-bold">Filter by Employment Status:</label>
                        </div>
                        <div class="col-auto">
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

                    <div class="col-auto ms-auto">
                        <a href="{{ route('manager.reports.export', array_merge(['report' => $report], request()->all())) }}" class="btn btn-success">
                            Export CSV
                        </a>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ---------------- SUMMARY CARD ---------------- --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">
                    @if($report === 'payment-history')
                        Payment Summary
                    @elseif($report === 'maintenance-requests')
                        Maintenance Requests Summary
                    @else
                        Summary
                    @endif
                </h5>

                <div class="text-muted small">
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
                </div>
            </div>

            <div class="text-end">
                @if($report === 'payment-history')
                    <div class="small text-muted">Total Paid</div>
                    <div class="fs-4 fw-bold">₱{{ number_format($total ?? 0, 2) }}</div>
                @else
                    <div class="small text-muted">Total</div>
                    <div class="fs-4 fw-bold">{{ $total ?? $data->total() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ---------------- TABLE ---------------- --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead class="table-light">
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
                                <th>Emergency Name</th>
                                <th>Emergency Number</th>
                                <th>Status</th>
                            </tr>
                        @elseif($report === 'lease-summary')
                            <tr>
                                <th>Tenant</th>
                                <th>Unit Type</th>
                                <th>Lease Start</th>
                                <th>Lease End</th>
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
                            @if($report === 'payment-history')
                                <tr>
                                    <td>{{ $item->tenant->name ?? 'N/A' }}</td>
                                    <td>₱{{ number_format($item->pay_amount, 2) }}</td>
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
                            @elseif(in_array($report, ['active-tenants', 'pending-tenants', 'rejected-tenants']))
                                @php $app = $item->tenantApplication; @endphp
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $app->contact_number ?? 'N/A' }}</td>
                                    <td>{{ $app->unit_type ?? 'N/A' }}</td>
                                    <td>{{ $app->employment_status ?? 'N/A' }}</td>
                                    <td>{{ $app->source_of_income ?? 'N/A' }}</td>
                                    <td>{{ $app->emergency_name ?? 'N/A' }}</td>
                                    <td>{{ $app->emergency_number ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($item->status) }}</td>
                                </tr>
                            @elseif($report === 'lease-summary')
                                @php
                                    $lease = $item->leases->first();
                                @endphp
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->tenantApplication->unit_type ?? 'N/A' }}</td>
                                    <td>{{ $lease?->lea_start_date ? \Carbon\Carbon::parse($lease->lea_start_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>{{ $lease?->lea_end_date ? \Carbon\Carbon::parse($lease->lea_end_date)->format('M d, Y') : 'N/A' }}</td>
                                </tr>
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

        {{-- ---------------- PAGINATION ---------------- --}}
        <div class="card-footer d-flex justify-content-center">
            {{ $data->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
