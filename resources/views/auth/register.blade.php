@extends('layouts.app')

@section('title', 'Register')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Body */
    body {
        background: linear-gradient(135deg, #f0f2f5, #d9e0eb);
        font-family: 'Inter', sans-serif;
    }

    /* Form Container */
    .registration-container {
        background: rgba(255,255,255,0.95);
        border-radius: 20px;
        padding: 50px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        backdrop-filter: blur(6px);
        max-width: 1200px;
        margin: auto;
    }

    /* Section Headers */
    .section-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #01017c, #2d3b9a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Inputs, Textareas, Selects */
    .form-control, .form-select, textarea {
        border-radius: 0.75rem;
        padding: 0.7rem 1rem;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus, textarea:focus {
        border-color: #01017c;
        box-shadow: 0 0 10px rgba(1,1,124,0.2);
    }

    /* Buttons */
    .btn-gradient {
        background: linear-gradient(135deg, #01017c, #2d3b9a);
        border: none;
        border-radius: 50px;
        padding: 0.65rem 2rem;
        font-weight: 600;
        color: #fff;
        transition: all 0.3s ease;
    }
    .btn-gradient:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(1,1,124,0.25);
    }

    /* Checkbox */
    .form-check-input {
        transform: scale(1.3);
        cursor: pointer;
    }

    /* File Inputs */
    input[type="file"] {
        border-radius: 0.75rem;
        padding: 0.4rem;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 1rem;
    }
    .modal-body iframe {
        border-radius: 0.5rem;
    }

    /* Responsive Column Stacking */
    @media (max-width: 768px) {
        .registration-container .row > .col-md-6 {
            border-right: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-5">
    <div class="registration-container">
        <h3 class="text-center mb-4 text-dark">Tenant Registration & Application</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <!-- LEFT SIDE -->
                <div class="col-md-6 border-end">
                    <h5 class="section-title">Account Information</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required
                               pattern="[a-zA-Z0-9]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                               title="Valid email format required">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact" class="form-control"
                               required maxlength="15" minlength="10"
                               pattern="^[0-9]{10,15}$"
                               title="Contact must be 10–15 digits only"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required
                               pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$"
                               title="At least 8 characters, with uppercase, lowercase, number & special character">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <!-- RIGHT SIDE -->
                <div class="col-md-6">
                    <h5 class="section-title">Tenant Application</h5>

                    <div class="mb-3">
                        <label class="form-label">Current Address</label>
                        <input type="text" name="current_address" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Birthdate</label>
                        <input type="date" name="birthdate" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Preferred Unit Type</label>
                        <select name="unit_type" id="unit_type" class="form-select" required>
                            <option value="">Select Unit Type</option>
                            @foreach($unitTypes as $type)
                                <option value="{{ $type }}" {{ old('unit_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Select Room</label>
                        <select name="unit_id" id="unit_id" class="form-select" required>
                            <option value="">Select a room</option>
                            @foreach($availableUnits as $unit)
                                <option value="{{ $unit->id }}"
                                        data-type="{{ $unit->type }}"
                                        {{ $unit->status === 'occupied' ? 'disabled' : '' }}
                                        {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->room_no }} ({{ $unit->type }})
                                    @if($unit->status === 'occupied') - Occupied @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Preferred Move-in Date</label>
                        <input type="date" name="move_in_date" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Reason for Renting</label>
                        <textarea name="reason" class="form-control" rows="2" required>{{ old('reason') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employment Status</label>
                        <select name="employment_status" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Employed" {{ old('employment_status') == 'Employed' ? 'selected' : '' }}>Employed</option>
                            <option value="Unemployed" {{ old('employment_status') == 'Unemployed' ? 'selected' : '' }}>Unemployed</option>
                            <option value="Student" {{ old('employment_status') == 'Student' ? 'selected' : '' }}>Student</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employer or School</label>
                        <input type="text" name="employer_school" class="form-control" value="{{ old('employer_school') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Source of Income</label>
                        <input type="text" name="source_of_income" class="form-control" value="{{ old('source_of_income') }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Emergency Contact Name</label>
                            <input type="text" name="emergency_name" class="form-control" value="{{ old('emergency_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Emergency Number</label>
                            <input type="text" name="emergency_number" class="form-control" value="{{ old('emergency_number') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Relationship</label>
                        <input type="text" name="emergency_relationship" class="form-control" value="{{ old('emergency_relationship') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Valid ID</label>
                        <input type="file" name="valid_id" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small class="text-muted">Accepted: JPG, PNG, PDF</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">1x1 ID Picture</label>
                        <input type="file" name="id_picture" class="form-control" accept="image/*" required>
                        <small class="text-muted">Image files only</small>
                    </div>
                </div>
            </div>

            <div class="form-check d-flex justify-content-center align-items-center gap-2 mb-4 mt-4">
                <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                <label class="form-check-label mb-0" for="terms">
                    I have read and agree to the
                    <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-decoration-underline">
                        Terms and Conditions
                    </a>.
                </label>
            </div>

            <div class="text-center mb-4">
                <button type="submit" class="btn btn-gradient">Submit Registration & Application</button>
            </div>

            <hr>

            <div class="text-center">
                <p class="mb-2">Already have an account?</p>
                <a href="{{ route('login') }}" class=" w-50">Login</a>
            </div>
        </form>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="height: 80vh;">
        <iframe src="{{ asset('storage/assets/tenant_agreement.pdf') }}" width="100%" height="100%" style="border:none;"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    // Filter rooms based on selected unit type
    const unitTypeSelect = document.getElementById('unit_type');
    const unitSelect = document.getElementById('unit_id');

    unitTypeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        Array.from(unitSelect.options).forEach(option => {
            if(option.value === "") return; // Keep placeholder
            option.style.display = option.dataset.type === selectedType ? 'block' : 'none';
        });
        unitSelect.value = ""; // reset selection
    });

    // Trigger change on page load to filter old value
    unitTypeSelect.dispatchEvent(new Event('change'));
</script>
@endpush
@endsection
