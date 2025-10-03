@extends('layouts.tenantdashboardlayout')

@section('title', 'My Requests')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span>Maintenance Requests</span>
        <!-- Create Request Button -->
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRequestModal">
            + Create Request
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($requests->isEmpty())
            <p>No requests found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Date Filed</th>
                            <th>Unit Type</th>
                            <th>Room No</th>
                            <th>Request</th>
                            <th>Urgency</th>
                            <th>Supposed Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y') }}</td>
                            <td>{{ $request->unit_type ?? '-' }}</td>
                            <td>{{ $request->room_no ?? '-' }}</td>
                            <td>{{ ucfirst($request->description) }}</td>
                            <td>
                                <span class="badge 
                                    @if($request->urgency === 'high') bg-danger 
                                    @elseif($request->urgency === 'mid') bg-warning text-dark 
                                    @else bg-success @endif">
                                    {{ ucfirst($request->urgency) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($request->supposed_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge 
                                    @if($request->status === 'Pending') bg-secondary 
                                    @elseif($request->status === 'Accepted') bg-success 
                                    @else bg-danger @endif">
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Create Request Modal -->
<div class="modal fade" id="createRequestModal" tabindex="-1" aria-labelledby="createRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('tenant.requests.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="createRequestModalLabel">New Maintenance Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Unit Type -->
                <div class="mb-3">
                    <label class="form-label">Unit Type</label>
                    <input type="text" name="unit_type" class="form-control" 
                        value="{{ old('unit_type', $unitType) }}" readonly>
                </div>

                <!-- Room Number -->
                <div class="mb-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_no" class="form-control" 
                        value="{{ old('room_no', $roomNo) }}" readonly>
                </div>


                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">What request are you asking for?</label>
                    <textarea name="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                </div>

                <!-- Urgency -->
                <div class="mb-3">
                    <label class="form-label">Urgency</label>
                    <select name="urgency" class="form-select" required>
                        <option value="" disabled selected>Select urgency</option>
                        <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="mid" {{ old('urgency') == 'mid' ? 'selected' : '' }}>Mid</option>
                        <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <!-- Supposed Date -->
                <div class="mb-3">
                    <label class="form-label">When should it happen?</label>
                    <input type="date" name="supposed_date" class="form-control" value="{{ old('supposed_date') }}" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
