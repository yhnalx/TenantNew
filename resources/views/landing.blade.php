@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="container py-5 text-center">



    <!-- Header Section -->
    <div class="mb-5">
        <h1 class="fw-bold display-5 text-primary">
            Welcome to <span class="text-gradient">Pinikitan Rental</span>
        </h1>
        <p class="lead text-muted">
            Find your next home with ease — we currently have
            <strong class="text-success">{{ $vacantCount }}</strong> vacant rooms available.
        </p>
    </div>

    <!-- Units Cards -->
    <div class="row justify-content-center g-4">
        @php
            $groupedUnits = $units->where('status', 'vacant')->groupBy('type');
        @endphp

        @foreach($groupedUnits as $type => $typeUnits)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 rounded-4 hover-card glass-card"
                     data-bs-toggle="modal"
                     data-bs-target="#unitModal"
                     data-type="{{ $type }}"
                     data-image="{{ asset('images/units/' . strtolower(str_replace(' ', '-', $type)) . '.jpg') }}"
                     data-available="{{ $typeUnits->where('status', 'vacant')->pluck('room_no')->join(', ') }}"
                     data-price="{{ $typeUnits->first()->room_price ?? 0 }}"
                     data-status="Vacant">

                    <img src="{{ asset('images/units/' . strtolower(str_replace(' ', '-', $type)) . '.jpg') }}"
                         class="card-img-top rounded-top-4"
                         alt="{{ $type }} Image"
                         style="height: 180px; object-fit: cover;">

                    <div class="card-body text-center">
                        <h4 class="card-title fw-bold">{{ $type }}</h4>
                        <span class="badge bg-success mb-3">Vacant</span>
                        <ul class="list-unstyled mb-0">
                            @foreach($typeUnits as $unit)
                                <li class="py-1">Room No: {{ $unit->room_no }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- About Us Section -->
    <section class="py-5 text-center">
        <h2 class="fw-bold text-primary mb-4">About Us</h2>
        <p class="lead text-muted mx-auto" style="max-width: 700px;">
            Our apartment complex started in <strong>2015</strong> with the goal of providing safe,
            affordable, and modern living spaces for students and working professionals.
            With well-maintained facilities and accessible locations, we’ve built a community
            where convenience meets comfort.
        </p>
    </section>

    <!-- Contact Section -->
    <section class="py-5 bg-light rounded-4 shadow-sm">
        <h2 class="fw-bold text-primary mb-4">📍 Contact Us</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <h5 class="fw-bold">Location</h5>
                    <p class="text-muted">123 Apartment Street, Cagayan de Oro, Philippines</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <h5 class="fw-bold">Phone</h5>
                    <p class="text-muted">+63 912 345 6789</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <h5 class="fw-bold">Email</h5>
                    <p class="text-muted">info@apartmentportal.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Embed -->
    <section class="py-5">
        <h2 class="fw-bold text-primary mb-4">📌 Find Us Here</h2>
        <div class="ratio ratio-16x9 rounded-4 shadow-sm overflow-hidden">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d829.5969257428177!2d124.65405466128895!3d8.475028046399832!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32fff2ce448339c1%3A0x639be33dade6bf70!2sR.A.%20VITORILLO%20LAW%20OFFICE%20AND%20ASSOCIATES!5e0!3m2!1sen!2sph!4v1761789546488!5m2!1sen!2sph"
                style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

</div>

<style>
    /* .modal-backdrop.show:nth-of-type(2) {
        z-index: 1056 !important;
    }
    #termsModal {
        z-index: 1057 !important;
    } */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border-radius: 1rem;
        box-shadow: 0 6px 25px rgba(49, 7, 236, 0.28);
    }
    .btn-gradient {
        background: linear-gradient(135deg, #01017c, #2d3b9a);
        color: #fff;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(1,1,124,0.25);
    }
    .text-gradient {
        background: linear-gradient(135deg, #01017c, #2d3b9a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<!-- Unit Modal (Reusable) -->
<div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg glass-card">

            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white" style="background: linear-gradient(135deg, #01017c, #2d3b9a);">
                <h5 class="modal-title fw-bold" id="unitModalTitle">Unit Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body p-4">
                <div class="row g-4">

                    <!-- LEFT: Unit Image + Info -->
                    <div class="col-lg-5 text-center">
                        <img id="unitModalImg" src="" alt="Unit Image"
                             class="img-fluid rounded-4 shadow-sm mb-3"
                             style="cursor: zoom-in;"
                             data-bs-toggle="modal" data-bs-target="#unitImageModal">

                        <div class="p-3 bg-light rounded-3 shadow-sm text-center">
                            <h6 class="fw-bold mb-2" id="unitModalTypeText"></h6>
                            <p class="mb-1"><strong>Size:</strong> <span id="unitModalSize"></span></p>
                            <p class="mb-1"><strong>Capacity:</strong> <span id="unitModalCapacity"></span></p>
                            <p class="mb-1"><strong>Available Rooms:</strong> <span id="unitModalAvailable"></span></p>
                            <p class="mb-1"><strong>Price:</strong> ₱<span id="unitModalPrice"></span></p>
                            <p class="mb-0"><strong>Status:</strong>
                                <span class="badge" id="unitModalStatus"></span>
                            </p>
                        </div>
                    </div>

                    <!-- RIGHT: Registration Form -->
                    <div class="col-lg-7">
                        <h5 class="mb-3 fw-bold text-primary">Tenant Registration & Application</h5>
                        <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">

                                <!-- FIRST NAME & LAST NAME -->
                                <div class="col-md-6">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" placeholder="Juan" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required>
                                </div>

                                <!-- EMAIL & CONTACT -->
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="you@example.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" name="contact" class="form-control" placeholder="09XXXXXXXXX" required>
                                </div>

                                <!-- PASSWORDS -->
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>

                                <!-- BIRTHDATE -->
                                <div class="col-md-6">
                                    <label class="form-label">Birthdate</label>
                                    <input type="date" name="birthdate" class="form-control" required>
                                </div>

                                <!-- ADDRESS & UNIT TYPE -->
                                <div class="col-md-6">
                                    <label class="form-label">Current Address</label>
                                    <input type="text" name="current_address" class="form-control" placeholder="Street, City" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Preferred Unit Type</label>
                                    <input type="text" name="unit_type" id="unitModalType" class="form-control" readonly>
                                </div>

                                <!-- FILTERED ROOM SELECTION -->
                                <div class="col-md-12">
                                    <label class="form-label">Select Room</label>
                                    <select name="unit_id" id="unit_id" class="form-select" required>
                                        <option value="">Select a room</option>
                                        @foreach($availableUnits as $unit)
                                            @if($unit->status === 'vacant')
                                                <option value="{{ $unit->id }}" data-type="{{ $unit->type }}">
                                                    {{ $unit->room_no }} ({{ $unit->type }})
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <!-- MOVE-IN DATE & REASON -->
                                <div class="col-md-6">
                                    <label class="form-label">Preferred Move-in Date</label>
                                    <input type="date" name="move_in_date" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Reason for Renting</label>
                                    <input type="text" name="reason" class="form-control" placeholder="For work, school, etc." required>
                                </div>

                                <!-- EMPLOYMENT INFO -->
                                <div class="col-md-6">
                                    <label class="form-label">Employment Status</label>
                                    <select name="employment_status" class="form-select" required>
                                        <option value="">Select</option>
                                        <option value="Employed">Employed</option>
                                        <option value="Unemployed">Unemployed</option>
                                        <option value="Student">Student</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Employer or School</label>
                                    <input type="text" name="employer_school" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Source of Income</label>
                                    <input type="text" name="source_of_income" class="form-control" required>
                                </div>

                                <!-- EMERGENCY CONTACT -->
                                <div class="col-md-6">
                                    <label class="form-label">Emergency Contact Name</label>
                                    <input type="text" name="emergency_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Emergency Number</label>
                                    <input type="text" name="emergency_number" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Relationship</label>
                                    <input type="text" name="emergency_relationship" class="form-control" required>
                                </div>

                                <!-- FILE UPLOADS -->
                                <div class="col-md-6">
                                    <label class="form-label">Valid ID</label>
                                    <input type="file" name="valid_id" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">1x1 ID Picture</label>
                                    <input type="file" name="id_picture" class="form-control" accept="image/*" required>
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

                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-gradient px-4">Submit Application</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
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

<!-- Zoom Image Modal -->
<div class="modal fade" id="unitImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body p-0">
                <img id="unitImageZoom" src="" alt="Unit Image Zoomed" class="img-fluid rounded-4 w-100">
            </div>
        </div>
    </div>
</div>


<!-- Additional Styles -->
@push('styles')
<style>
    /* Card Hover Effect */
    .hover-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        cursor: pointer;
    }
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 16px 30px rgba(0,0,0,0.15);
    }

    /* Glass Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border-radius: 1rem;
        box-shadow: 0 6px 25px rgba(49, 7, 236, 0.28);
    }

    /* Gradient Text */
    .text-gradient {
        background: linear-gradient(135deg, #01017c, #2d3b9a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Buttons */
    .btn-gradient {
        background: linear-gradient(135deg, #01017c, #2d3b9a);
        color: #fff;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(1,1,124,0.25);
    }
</style>
@endpush

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {
    const termsModal = document.getElementById('termsModal');
    const unitModal = document.getElementById('unitModal');

    // When "Terms and Conditions" modal opens → hide the registration modal
    termsModal.addEventListener('show.bs.modal', function () {
        const modal = bootstrap.Modal.getInstance(unitModal);
        if (modal) modal.hide();
    });

    // When closing Terms modal → reopen Registration modal
    termsModal.addEventListener('hidden.bs.modal', function () {
        const modal = new bootstrap.Modal(unitModal);
        modal.show();
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const termsModal = document.getElementById('termsModal');
    const unitModal = document.getElementById('unitModal');

    // When "Terms and Conditions" modal opens → hide the registration modal
    termsModal.addEventListener('show.bs.modal', function () {
        const modal = bootstrap.Modal.getInstance(unitModal);
        if (modal) modal.hide();
    });

    // When closing Terms modal → reopen Registration modal
    termsModal.addEventListener('hidden.bs.modal', function () {
        const modal = new bootstrap.Modal(unitModal);
        modal.show();
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const unitModalEl = document.getElementById('unitModal');
    const zoomModalEl = document.getElementById('unitImageModal');
    const zoomImg = document.getElementById('unitImageZoom');

    // Bootstrap modal instances
    const zoomModal = new bootstrap.Modal(zoomModalEl);

    const unitInfo = {
        "Studio": { size: "25 m²", capacity: "2 persons" },
        "1 Bedroom": { size: "35 m²", capacity: "2–3 persons" },
        "2 Bedroom": { size: "45 m²", capacity: "4 persons" }
    };

    unitModalEl.addEventListener('show.bs.modal', event => {
        const card = event.relatedTarget; // clicked card
        const type = card.dataset.type;
        const image = card.dataset.image;
        const available = card.dataset.available;
        const price = card.dataset.price;
        const status = card.dataset.status;

        document.getElementById('unitModalTitle').textContent = `${type} Unit`;
        const modalImg = document.getElementById('unitModalImg');
        modalImg.src = image;
        document.getElementById('unitModalTypeText').textContent = `${type} Unit`;

        const info = unitInfo[type] || { size: "N/A", capacity: "N/A" };
        document.getElementById('unitModalSize').textContent = info.size;
        document.getElementById('unitModalCapacity').textContent = info.capacity;

        document.getElementById('unitModalAvailable').textContent = available;
        document.getElementById('unitModalPrice').textContent = Number(price).toFixed(2);

        const statusBadge = document.getElementById('unitModalStatus');
        statusBadge.textContent = status;
        statusBadge.className = 'badge ' + (status.toLowerCase() === 'vacant' ? 'bg-success' : 'bg-danger');

        document.getElementById('unitModalType').value = type;

        // Zoom image
        modalImg.onclick = function() {
            zoomImg.src = modalImg.src;
            zoomModal.show();
        };
    });

    // Filter available rooms by unit type dynamically
    unitModalEl.addEventListener('show.bs.modal', event => {
        const card = event.relatedTarget;
        const type = card.dataset.type;
        const select = document.getElementById('unit_id');

        // Reset room list
        [...select.options].forEach(opt => {
            if (opt.value === "") return; // Skip "Select a room"
            const matchesType = opt.dataset.type === type;
            opt.hidden = !matchesType; // Hide rooms that don't match
        });

        // Automatically select blank option
        select.value = "";
    });


});
</script>
@endpush


@endSection
