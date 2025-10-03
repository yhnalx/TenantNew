@extends('layouts.managerdashboardlayout')

@section('title', 'Manage Units')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .card { border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
    .table thead th { text-align: center; vertical-align: middle; }
    .badge { font-size: 0.9rem; padding: 0.5em 1em; }
    .modal-content { border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
    .modal-header { border-bottom: none; background: #0d6efd; color: #fff; border-top-left-radius: 12px; border-top-right-radius: 12px; }
    .modal-footer { border-top: none; }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold text-primary">🏠 Unit Management</h2>

    <!-- Add Unit Button -->
    <button type="button" class="btn btn-primary mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createUnitModal">
        <i class="bi bi-plus-circle"></i> Add New Unit
    </button>

    <!-- Units Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Room No</th>
                            <th>Room Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr>
                            <td>{{ $unit->id }}</td>
                            <td>{{ $unit->type }}</td>
                            <td><span class="fw-bold">{{ $unit->room_no }}</span></td>
                            <td><span class="text-success fw-semibold">₱{{ number_format($unit->room_price, 2) }}</span></td>
                            <td>
                                <span class="badge {{ $unit->status == 'vacant' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($unit->status) }}
                                </span>
                            </td>
                            <td>
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-sm btn-warning me-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editUnitModal{{ $unit->id }}"
                                    title="Edit Unit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteUnitModal{{ $unit->id }}"
                                    title="Delete Unit">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-muted text-center">No units available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- CREATE modal -->
<div class="modal fade" id="createUnitModal" tabindex="-1" aria-labelledby="createUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('manager.units.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add Unit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Type Dropdown -->
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select Type</option>
                            @foreach(['Studio','1-Bedroom','2-Bedroom','Penthouse'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Room No</label>
                        <input type="text" name="room_no" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Room Price</label>
                        <input type="number" step="0.01" name="room_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="vacant">Vacant</option>
                            <option value="occupied">Occupied</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Unit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- EDIT & DELETE modals -->
@foreach($units as $unit)
    <!-- Edit Modal -->
    <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1" aria-labelledby="editUnitModalLabel{{ $unit->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('manager.units.update', $unit->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Unit</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Type Dropdown -->
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                @foreach(['Studio','1-Bedroom','2-Bedroom','Penthouse'] as $type)
                                    <option value="{{ $type }}" {{ $unit->type === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Room No</label>
                            <input type="text" name="room_no" class="form-control" value="{{ $unit->room_no }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Room Price</label>
                            <input type="number" step="0.01" name="room_price" class="form-control" value="{{ $unit->room_price }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="vacant" {{ $unit->status == 'vacant' ? 'selected' : '' }}>Vacant</option>
                                <option value="occupied" {{ $unit->status == 'occupied' ? 'selected' : '' }}>Occupied</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteUnitModal{{ $unit->id }}" tabindex="-1" aria-labelledby="deleteUnitModalLabel{{ $unit->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('manager.units.destroy', $unit->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white"><i class="bi bi-trash3-fill"></i> Delete Unit</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to delete this unit?</p>
                        <p class="fw-bold text-danger">Room No: {{ $unit->room_no }}</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipEls.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush
