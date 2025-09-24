@extends('layouts.tenantdashboardlayout')

@section('title', 'Tenant Dashboard')

@section('content')
<div class="container-fluid pt-4">

    {{-- Welcome --}}
    <div class="text-center mb-4">
        <h2 class="fw-bold">Welcome, {{ Auth::user()->name }} 👋</h2>
        <p class="text-muted">Here’s your tenant dashboard overview</p>
    </div>

    @php
        $user = Auth::user();
        $payments = $payments ?? collect();
        $requests = $requests ?? collect();

        // Check if the tenant application is complete
        $tenantApplication = \App\Models\TenantApplication::where('user_id', $user->id)->first();
        $showApplicationModal = !$tenantApplication || !$tenantApplication->is_complete;
    @endphp

    {{-- Tenant Info --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">Your Information</div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Status:</strong>
                @if($user->status === 'approved')
                    <span class="badge bg-success">Approved ✅</span>
                @elseif($user->status === 'pending')
                    <span class="badge bg-warning text-dark">Pending ⏳</span>
                @else
                    <span class="badge bg-danger">Rejected ❌</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Payments</h5>
                    <p class="card-text fs-3">₱{{ number_format($payments->sum('amount'), 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Pending Requests</h5>
                    <p class="card-text fs-3">{{ $requests->where('status','pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-secondary shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Requests</h5>
                    <p class="card-text fs-3">{{ $requests->count() }}</p>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Tenant Application Modal --}}
@if($showApplicationModal)
<div class="modal fade" id="tenantApplicationModal" tabindex="-1" aria-labelledby="tenantApplicationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="tenantApplicationModalLabel">Tenant Application Form</h5>
      </div>
      <div class="modal-body">
        <form id="tenantApplicationForm" enctype="multipart/form-data">
          @csrf

          <!-- Full Name (read-only) -->
          <div class="mb-3">
              <label for="full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $user->name }}" readonly>
          </div>

          <!-- Email -->
          <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
          </div>

          <!-- Contact Number -->
          <div class="mb-3">
              <label for="contact_number" class="form-label">Contact Number</label>
              <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ $user->contact_number }}"required>
          </div>

          <!-- Current Address -->
          <div class="mb-3">
              <label for="current_address" class="form-label">Current Address</label>
              <input type="text" class="form-control" id="current_address" name="current_address" required>
          </div>

          <!-- Birthdate -->
          <div class="mb-3">
              <label for="birthdate" class="form-label">Birthdate</label>
              <input type="date" class="form-control" id="birthdate" name="birthdate" required>
          </div>

          <!-- Preferred Unit Type -->
          <div class="mb-3">
              <label for="unit_type" class="form-label">Preferred Unit Type</label>
              <select class="form-select" id="unit_type" name="unit_type" required>
                  <option value="">Select Unit Type</option>
                  <option value="Studio">Studio</option>
                  <option value="One Bedroom">One Bedroom</option>
                  <option value="Two Bedroom">Two Bedroom</option>
              </select>
          </div>

          <!-- Preferred Move-in Date -->
          <div class="mb-3">
              <label for="move_in_date" class="form-label">Preferred Move-in Date</label>
              <input type="date" class="form-control" id="move_in_date" name="move_in_date" required>
          </div>

          <!-- Reason for Renting -->
          <div class="mb-3">
              <label for="reason" class="form-label">Reason for Renting</label>
              <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
          </div>

          <!-- Employment Status -->
          <div class="mb-3">
              <label for="employment_status" class="form-label">Employment Status</label>
              <select class="form-select" id="employment_status" name="employment_status" required>
                  <option value="">Select Employment Status</option>
                  <option value="Employed">Employed</option>
                  <option value="Unemployed">Unemployed</option>
                  <option value="Student">Student</option>
              </select>
          </div>

          <!-- Employer or School -->
          <div class="mb-3">
              <label for="employer_school" class="form-label">Employer or School</label>
              <input type="text" class="form-control" id="employer_school" name="employer_school" required>
          </div>

          <!-- Emergency Contact Name -->
          <div class="mb-3">
              <label for="emergency_name" class="form-label">Emergency Contact Name</label>
              <input type="text" class="form-control" id="emergency_name" name="emergency_name" required>
          </div>

          <!-- Emergency Contact Number -->
          <div class="mb-3">
              <label for="emergency_number" class="form-label">Emergency Contact Number</label>
              <input type="text" class="form-control" id="emergency_number" name="emergency_number" required>
          </div>

          <!-- Emergency Relationship -->
          <div class="mb-3">
              <label for="emergency_relationship" class="form-label">Relationship</label>
              <input type="text" class="form-control" id="emergency_relationship" name="emergency_relationship" required>
          </div>

          <!-- Valid ID Upload -->
          <div class="mb-3">
              <label for="valid_id" class="form-label">Valid ID</label>
              <input type="file" class="form-control" id="valid_id" name="valid_id" accept=".jpg,.jpeg,.png,.pdf" required>
              <small class="text-muted">Accepted: JPG, PNG, PDF</small>
              <div id="validIdPreview" class="mt-2"></div>
          </div>

          <!-- 1x1 ID Picture Upload -->
          <div class="mb-3">
              <label for="id_picture" class="form-label">1x1 ID Picture</label>
              <input type="file" class="form-control" id="id_picture" name="id_picture" accept="image/*" required>
              <small class="text-muted">Accepted: Image files only</small>
              <div id="idPicturePreview" class="mt-2"></div>
          </div>

          <div class="text-end">
              <button type="submit" class="btn btn-primary">Submit Application</button>
          </div>

        </form>
        <div id="formAlert" class="alert mt-3 d-none"></div>
      </div>
    </div>
  </div>
</div>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('tenantApplicationModal');
    var tenantModal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
    tenantModal.show();

    // File preview function
    function previewFile(inputEl, previewEl, maxWidth=150) {
        inputEl.addEventListener('change', function () {
            previewEl.innerHTML = '';
            const file = this.files[0];
            if(file) {
                if(file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.maxWidth = maxWidth + 'px';
                    img.classList.add('img-thumbnail', 'mt-2');
                    previewEl.appendChild(img);
                } else {
                    previewEl.innerHTML = `<p class="text-muted">Selected file: ${file.name}</p>`;
                }
            }
        });
    }

    previewFile(document.getElementById('valid_id'), document.getElementById('validIdPreview'), 200);
    previewFile(document.getElementById('id_picture'), document.getElementById('idPicturePreview'), 150);

    // AJAX form submission
    const form = document.getElementById('tenantApplicationForm');
    const alertBox = document.getElementById('formAlert');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch("{{ route('tenant.application.submit') }}", {
            method: "POST",
            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
            body: formData
        })
        .then(async response => {
            const text = await response.text();
            try { return JSON.parse(text); } 
            catch(err) { throw new Error("Response is not JSON: " + err.message); }
        })
        .then(data => {
            if (data.success) {
                alertBox.classList.remove('d-none','alert-danger');
                alertBox.classList.add('alert-success');
                alertBox.textContent = data.message;

                setTimeout(() => {
                    tenantModal.hide();
                    alertBox.classList.add('d-none');
                    location.reload();
                }, 2000);
            } else {
                alertBox.classList.remove('d-none','alert-success');
                alertBox.classList.add('alert-danger');
                alertBox.textContent = data.message || "Something went wrong!";
            }
        })
        .catch(error => {
            alertBox.classList.remove('d-none','alert-success');
            alertBox.classList.add('alert-danger');
            alertBox.textContent = "Error: " + error.message;
        });
    });
});
</script>
@endif

@endsection
