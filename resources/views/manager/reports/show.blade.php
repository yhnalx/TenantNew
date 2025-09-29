@extends('layouts.managerdashboardlayout')

@section('title', $title)
@section('page-title', $title)

@section('content')
<div class="container">
    <h4>{{ $title }}</h4>
    <hr>

    @if($report === 'active-tenants')
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th>Unit Type</th>
                    <th>Lease (placeholder)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $tenant)
                    <tr>
                        <td>{{ $tenant->name }}</td>
                        <td>{{ $tenant->tenantApplication->unit_type ?? 'N/A' }}</td>
                        <td>{{ 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif($report === 'payment-history')
    <!-- 🔽 Filter Form (auto submit on change) -->
    <form method="GET" action="{{ route('manager.reports.show', ['report' => 'payment-history']) }}" class="mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label for="payment_for" class="col-form-label">Filter by Purpose:</label>
            </div>
            <div class="col-auto">
                <select name="payment_for" id="payment_for" class="form-select" onchange="this.form.submit()">
                    <option value="">All</option>
                    <option value="rent" {{ request('payment_for') === 'rent' ? 'selected' : '' }}>Rent</option>
                    <option value="utilities" {{ request('payment_for') === 'utilities' ? 'selected' : '' }}>Utilities</option>
                    <option value="others" {{ request('payment_for') === 'others' ? 'selected' : '' }}>Others</option>
                </select>
            </div>
            <div class="col-auto">
                <!-- Export button preserves filter -->
                <a href="{{ route('manager.reports.export', ['report' => 'payment-history', 'payment_for' => request('payment_for')]) }}" 
                   class="btn btn-success">
                    Export CSV
                </a>
            </div>
        </div>
    </form>


        <!-- Summary Card -->
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Summary</h5>
                    <div class="text-muted small">
                        Showing: <strong>{{ $currentFilter ? ucfirst($currentFilter) : 'All Categories' }}</strong>
                    </div>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Total Paid</div>
                    <div class="fs-4 fw-bold">₱{{ number_format($total ?? 0, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Paginated Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tenant</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $payment)
                    <tr>
                        <td>{{ $payment->tenant->name ?? 'N/A' }}</td>
                        <td>₱{{ number_format($payment->pay_amount, 2) }}</td>
                        <td>{{ $payment->pay_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td>{{ ucfirst($payment->payment_for) }}</td>
                        <td>
                            <form action="{{ route('manager.payments.updateStatus', $payment->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="pay_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="Pending" {{ $payment->pay_status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Accepted" {{ $payment->pay_status === 'Accepted' ? 'selected' : '' }}>Accepted</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>


        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $data->appends(request()->query())->links() }}
        </div>

    @elseif($report === 'maintenance-requests')
    <div class="container mt-4">

        <!-- 🔽 Filter Form -->
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('manager.reports.show', ['report' => 'maintenance-requests']) }}" class="row g-2 align-items-center">
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

                    <div class="col-auto ms-auto">
                        <!-- Export button -->
                        <a href="{{ route('manager.reports.export', [
                            'report' => 'maintenance-requests',
                            'status' => request('status'),
                            'urgency' => request('urgency')
                        ]) }}" class="btn btn-success">
                            Export CSV
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- 🔽 Summary Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">Maintenance Requests Summary</h5>
                    <div class="text-muted small">
                        Status: <strong>{{ request('status') ?: 'All' }}</strong> | 
                        Urgency: <strong>{{ request('urgency') ?: 'All' }}</strong>
                    </div>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Total Requests</div>
                    <div class="fs-4 fw-bold">{{ $total ?? $data->total() }}</div>
                </div>
            </div>
        </div>

        <!-- 🔽 Requests Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Maintenance Requests</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tenant</th>
                                <th>Description</th>
                                <th>Urgency</th>
                                <th>Supposed Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $request)
                                <tr>
                                    <td>{{ $request->tenant->name ?? 'N/A' }}</td>
                                    <td>{{ $request->description }}</td>
                                    <td>
                                        <span class="badge bg-{{ $request->urgency === 'high' ? 'danger' : ($request->urgency === 'mid' ? 'warning text-dark' : 'secondary') }}">
                                            {{ ucfirst($request->urgency) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($request->supposed_date)->format('M d, Y') }}</td>
                                    <td>
                                        <form action="{{ route('manager.requests.updateStatus', $request->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="Pending" {{ $request->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="In Progress" {{ $request->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="Completed" {{ $request->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('manager.requests.show', $request->id) }}" class="btn btn-sm btn-outline-info">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">No maintenance requests found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer">
                {{ $data->links() }}
            </div>
        </div>
    </div>

    <!-- Pagination -->
    {{-- <div class="d-flex justify-content-center">
        {{ $data->appends(request()->query())->links() }}
    </div> --}}

    @else
        <p>Coming soon...</p>
    @endif
</div>
@endsection
