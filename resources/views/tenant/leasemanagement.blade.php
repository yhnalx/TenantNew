@extends('layouts.tenantdashboardlayout')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Lease Management</h2>

    <!-- Apply for New Lease Button -->
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#applyLeaseModal">
        <i class="bi bi-plus-circle me-1"></i> Apply for New Lease
    </button>

    @if($leases->isEmpty())
        <div class="alert alert-info">
            You currently have no active leases.
        </div>
    @else
        <div class="row">
            @foreach($leases as $lease)
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="fw-bold">{{ $lease->unit->unit_type }}</h5>
                            <p class="mb-1">Room No: <strong>{{ $lease->unit->room_no }}</strong></p>
                            <p class="mb-1">Monthly Rent:
                                <strong>₱{{ number_format($lease->unit->room_price, 2) }}</strong>
                            </p>
                            <p class="mb-1">Lease Start:
                                <strong>{{ $lease->lea_start_date }}</strong>
                            </p>
                            <p class="mb-2">Lease End:
                                <strong>{{ $lease->lea_end_date }}</strong>
                            </p>
                            <p class="text-muted">
                                Status: <strong>{{ $lease->lea_status }}</strong>
                            </p>

                            <a href="#" class="btn btn-primary btn-sm mt-2">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>


<!-- ===================================== -->
<!-- Apply for New Lease Modal -->
<!-- ===================================== -->

<div class="modal fade" id="applyLeaseModal" tabindex="-1" aria-labelledby="applyLeaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="applyLeaseModalLabel">Apply for New Lease</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('tenant.leases.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Unit</label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">-- Choose Unit --</option>

                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit->id }}">
                                    {{ $unit->type }} {{ $unit->room_no }} - {{ $unit->unit_type }}
                                    (₱{{ number_format($unit->room_price, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Lease Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
