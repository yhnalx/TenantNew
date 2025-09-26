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
        <!-- Filter (auto-submit on change) -->
        <form method="GET" action="{{ route('manager.reports.show', ['report' => 'payment-history']) }}" class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label for="payment_for" class="col-form-label">Filter by Purpose:</label>
                </div>
                <div class="col-auto">
                    <select name="payment_for" id="payment_for" class="form-select" onchange="this.form.submit()">
                        <option value="" {{ $currentFilter === '' ? 'selected' : '' }}>All</option>
                        <option value="rent" {{ $currentFilter === 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="utilities" {{ $currentFilter === 'utilities' ? 'selected' : '' }}>Utilities</option>
                        <option value="others" {{ $currentFilter === 'others' ? 'selected' : '' }}>Others</option>
                    </select>
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
                </tr>
            </thead>
            <tbody>
                @forelse($data as $payment)
                    <tr>
                        <td>{{ $payment->tenant->name ?? 'N/A' }}</td>
                        <td>₱{{ number_format($payment->pay_amount, 2) }}</td>
                        <td>{{ $payment->pay_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td>{{ ucfirst($payment->payment_for) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="d-flex justify-content-center">
            {{ $data->appends(request()->query())->links() }}
        </div>

    @elseif($report === 'maintenance-requests')
        <div class="alert alert-info text-center">
            🚧 Maintenance Requests report is <strong>Coming Soon...</strong>
        </div>

    @else
        <p>Coming soon...</p>
    @endif
</div>
@endsection
