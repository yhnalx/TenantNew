@extends('layouts.managerdashboardlayout')

@section('title', 'Utilities Management')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ========= GLOBAL ========== */
body {
    background: #f3f5f9;
    font-family: 'Inter', sans-serif;
}

/* Gradient Text */
h2.gradient-text, .card-header.gradient-text, .modal-title.gradient-text {
    background: linear-gradient(90deg, #0d6efd, #00b4d8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
}

/* ========= CARD DESIGN ========== */
.card {
    border: none;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.card-header {
    border: none;
    border-radius: 16px 16px 0 0;
    background: #fff; /* we use gradient only on text */
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* ========= TABLE DESIGN ========== */
.table thead th {
    background: #fff7e0;
    color: #343a40;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.875rem;
}
.table tbody tr:hover {
    background: #fffde8;
    transition: 0.25s ease-in;
}
.table td, .table th {
    vertical-align: middle;
}
.table .text-end {
    font-weight: 600;
}

/* ========= BUTTONS ========== */
.btn {
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
}
.btn:hover {
    transform: translateY(-1px);
}
.btn-primary {
    background: linear-gradient(135deg, #0d6efd, #00b4d8);
    border: none;
    color: #fff;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #00b4d8, #0d6efd);
}
.btn-outline-secondary {
    border-radius: 10px;
}

/* ========= MODAL DESIGN ========== */
.modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.modal-header {
    border-top-left-radius: 16px;
    border-top-right-radius: 16px;
    background: #fff; /* gradient applied to title */
}
.modal-footer {
    border-top: none;
    background-color: #f8f9fa;
}
.form-floating label {
    color: #666;
}

/* ========= ALERT DESIGN ========== */
#alertContainer .alert {
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}

/* ========= RESPONSIVE ========= */
@media (max-width: 768px) {
    h2 { font-size: 1.4rem; }
    .btn-sm { font-size: 0.8rem; padding: 0.3rem 0.6rem; }
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <h2 class="gradient-text mb-4">
        Utilities Management
    </h2>

    <div class="card mb-4">
        <div class="card-header gradient-text">
            <i class="bi bi-list-check"></i> Tenant Utilities Overview
        </div>
        <div class="card-body">
            <div id="alertContainer"></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>Tenant Name</th>
                            <th>Room No</th>
                            <th>Utility Balance (₱)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leases as $lease)
                        <tr id="lease-row-{{ $lease->id }}">
                            <td>{{ $lease->tenant->name }}</td>
                            <td>{{ $lease->room_no ?? 'N/A' }}</td>
                            <td class="text-end">₱{{ number_format($lease->tenant->utility_balance, 2) }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-btn"
                                    data-id="{{ $lease->id }}"
                                    data-name="{{ $lease->tenant->name }}"
                                    data-balance="{{ number_format($lease->utility_balance, 2, '.', '') }}">
                                    <i class="bi bi-pencil-square me-1"></i> Update
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-muted py-4">No tenant utility records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Update Utility Modal -->
<div class="modal fade" id="editUtilityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title gradient-text">
                    <i class="bi bi-pencil-square me-1"></i> Update Utility Balance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUtilityForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="leaseId">
                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="text" id="tenantName" class="form-control" readonly>
                        <label for="tenantName">Tenant Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" id="utilityBalance" class="form-control text-end" placeholder="0.00" inputmode="decimal" required>
                        <label for="utilityBalance">Utility Balance (₱)</label>
                    </div>
                    <div class="mb-3">
                        <label for="proofOfUtilityBilling" class="form-label fw-semibold">Proof of Utility Billing (Optional)</label>
                        <input type="file" id="proofOfUtilityBilling" name="proof_of_utility_billing" class="form-control" accept="image/*">
                        <small class="text-muted">Upload an image of the utility bill (JPG, PNG, max 2MB).</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = new bootstrap.Modal(document.getElementById('editUtilityModal'));
    const editForm = document.getElementById('editUtilityForm');
    const tenantNameInput = document.getElementById('tenantName');
    const utilityBalanceInput = document.getElementById('utilityBalance');
    const proofInput = document.getElementById('proofOfUtilityBilling');
    const leaseIdInput = document.getElementById('leaseId');
    const alertContainer = document.getElementById('alertContainer');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            leaseIdInput.value = button.dataset.id;
            tenantNameInput.value = button.dataset.name;
            utilityBalanceInput.value = parseFloat(button.dataset.balance).toLocaleString('en-PH', {minimumFractionDigits:2});
            proofInput.value = '';
            modal.show();
        });
    });

    utilityBalanceInput.addEventListener('input', function () {
        const val = this.value.replace(/,/g, '');
        if (!isNaN(val) && val !== '') {
            this.value = parseFloat(val).toLocaleString('en-PH', {minimumFractionDigits:2});
        }
    });

    editForm.addEventListener('submit', function(e){
        e.preventDefault();
        const leaseId = leaseIdInput.value;
        const formattedValue = parseFloat(utilityBalanceInput.value.replace(/,/g,''));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        if(isNaN(formattedValue)){
            alertContainer.innerHTML = `<div class="alert alert-danger">Invalid number entered.</div>`;
            return;
        }

        const formData = new FormData();
        formData.append('utility_balance', formattedValue);
        formData.append('_method', 'PUT');
        if (proofInput.files[0]) {
            formData.append('proof_of_utility_billing', proofInput.files[0]);
        }

        fetch(`/manager/utilities/${leaseId}`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrfToken},
            body: formData
        })
        .then(r=>r.json())
        .then(data=>{
            if(data.success){
                document.querySelector(`#lease-row-${leaseId} .text-end`).textContent =
                    '₱'+formattedValue.toLocaleString('en-PH',{minimumFractionDigits:2});
                modal.hide();
                alertContainer.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show mt-3">
                        <i class="bi bi-check-circle me-1"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
            } else {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show mt-3">
                        <i class="bi bi-exclamation-triangle me-1"></i> ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
            }
        })
        .catch(err=>{
            console.error(err);
            alertContainer.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                    <i class="bi bi-x-circle me-1"></i> An error occurred while updating.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
        });
    });
});
</script>
@endsection
