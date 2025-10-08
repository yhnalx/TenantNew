@extends('layouts.tenantdashboardlayout')

@section('title', 'My Requests')

@section('content')
<div class="card mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <span class="fw-bold">Maintenance Requests</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRequestModal">
            + Create Request
        </button>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($requests->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="bi bi-exclamation-circle fs-1 mb-2"></i>
                <p class="mb-0">No maintenance requests found. Click <strong>"Create Request"</strong> to add one.</p>
            </div>
        @else
        @if(!$requests->isEmpty())
            <div class="mb-3 d-flex justify-content-end">
                <input type="text" id="requestSearch" class="form-control w-25" placeholder="Search requests...">
            </div>
        @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle text-center">
                    <thead class="table-light">
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
                        @php
                            $urgencyClass = match($request->urgency) {
                                'high' => 'bg-danger text-white',
                                'mid' => 'bg-warning text-dark',
                                default => 'bg-success text-white',
                            };
                            $statusClass = match($request->status) {
                                'Pending' => 'bg-secondary text-white',
                                'Accepted' => 'bg-success text-white',
                                'Rejected' => 'bg-danger text-white',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <tr @if($request->urgency === 'high') class="table-danger" @endif>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->created_at)->format('D, M d, Y') }}</td>
                            <td>{{ $request->unit_type ?? '-' }}</td>
                            <td>{{ $request->room_no ?? '-' }}</td>
                            <td>{{ ucfirst($request->description) }}</td>
                            <td><span class="badge {{ $urgencyClass }}">{{ ucfirst($request->urgency) }}</span></td>
                            <td>{{ \Carbon\Carbon::parse($request->supposed_date)->format('D, M d, Y') }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ ucfirst($request->status) }}</span></td>
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
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('tenant.requests.store') }}" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createRequestModalLabel">New Maintenance Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Unit Type</label>
                    <input type="text" name="unit_type" class="form-control" value="{{ old('unit_type', $unitType) }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_no" class="form-control" value="{{ old('room_no', $roomNo) }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Request Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Describe the issue..." required>{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Urgency</label>
                    <select name="urgency" class="form-select" required>
                        <option value="" disabled selected>Select urgency</option>
                        <option value="low" {{ old('urgency') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="mid" {{ old('urgency') == 'mid' ? 'selected' : '' }}>Mid</option>
                        <option value="high" {{ old('urgency') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Supposed Date</label>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('requestSearch');
    const table = document.querySelector('table tbody');
    const rows = table ? Array.from(table.querySelectorAll('tr')) : [];

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // Optional: Simple column sorting
    document.querySelectorAll('table thead th').forEach((th, index) => {
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => {
            const sortedRows = rows.sort((a, b) => {
                const aText = a.children[index].textContent.trim();
                const bText = b.children[index].textContent.trim();
                return aText.localeCompare(bText, undefined, { numeric: true });
            });
            sortedRows.forEach(row => table.appendChild(row));
        });
    });
});
</script>
@endpush
