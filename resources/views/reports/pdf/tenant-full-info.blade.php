<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tenant Data Sheet</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 40px;
            color: #222;
            background-color: #fff;
        }

        h1, h2, h3 {
            color: #0d6efd;
            text-align: center;
        }

        h1 {
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        hr {
            border: none;
            border-top: 2px solid #0d6efd;
            margin: 15px 0;
        }

        p {
            margin: 4px 0;
            line-height: 1.5;
        }

        b {
            color: #333;
        }

        .section {
            margin-top: 25px;
        }

        .section-title {
            text-transform: uppercase;
            text-decoration: underline;
            color: #0d6efd;
            font-weight: bold;
        }

        .id-section {
            text-align: center;
            margin-top: 20px;
        }

        .id-section img {
            width: 260px;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: 10px;
        }

        .status {
            font-weight: bold;
            text-transform: capitalize;
        }

        .status.pending {
            color: #856404;
        }

        .status.approved {
            color: #0f5132;
        }

        .status.rejected {
            color: #842029;
        }

        .footer {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <h1>Tenant Bio Data</h1>
    <hr>

    <div class="section">
        <h3 class="section-title">Personal Details</h3>
        <p><b>Full Name:</b> {{ $tenantApp->full_name }}</p>
        <p><b>Email:</b> {{ $tenantApp->email }}</p>
        <p><b>Contact Number:</b> {{ $tenantApp->contact_number }}</p>
        <p><b>Current Address:</b> {{ $tenantApp->current_address }}</p>
        <p><b>Birthdate:</b> {{ \Carbon\Carbon::parse($tenantApp->birthdate)->format('F d, Y') }}</p>
    </div>

    <hr>

    <div class="section">
        <h3 class="section-title">Application Information</h3>
        <p><b>Unit Type:</b> {{ $tenantApp->unit_type }}</p>
        <p><b>Room No:</b> {{ $tenantApp->room_no ?? 'N/A' }}</p>
        <p><b>Move-In Date:</b> {{ \Carbon\Carbon::parse($tenantApp->move_in_date)->format('F d, Y') }}</p>
        <p><b>Reason for Moving:</b> {{ $tenantApp->reason }}</p>
    </div>

    <hr>

    <div class="section">
        <h3 class="section-title">Employment & Income</h3>
        <p><b>Employment Status:</b> {{ $tenantApp->employment_status }}</p>
        <p><b>Employer / School:</b> {{ $tenantApp->employer_school }}</p>
        <p><b>Source of Income:</b> {{ $tenantApp->source_of_income }}</p>
    </div>

    <hr>

    <div class="section">
        <h3 class="section-title">Emergency Contact</h3>
        <p><b>Contact Name:</b> {{ $tenantApp->emergency_name }}</p>
        <p><b>Relationship:</b> {{ $tenantApp->emergency_relationship }}</p>
        <p><b>Contact Number:</b> {{ $tenantApp->emergency_number }}</p>
    </div>

    <hr>

    <div class="section id-section">
        <h3 class="section-title">Uploaded Identification</h3>
        @if($tenantApp->valid_id_path)
            <p><b>Valid ID:</b></p>
            <img src="{{ public_path('storage/' . $tenantApp->valid_id_path) }}" alt="Valid ID">
        @endif
        @if($tenantApp->id_picture_path)
            <p><b>ID Picture:</b></p>
            <img src="{{ public_path('storage/' . $tenantApp->id_picture_path) }}" alt="ID Picture">
        @endif
    </div>

    <hr>

    <div class="section">
        <h3 class="section-title">Application Status</h3>
        <p><b>Status:</b> 
            <span class="status {{ $tenant->status }}">
                {{ ucfirst($tenant->status) }}
            </span>
        </p>
        @if($tenant->rejection_reason)
            <p><b>Rejection Reason:</b> {{ $tenant->rejection_reason }}</p>
        @endif
    </div>

    <hr>
    <div class="footer">
        Generated on {{ now()->format('F d, Y') }} | Property Management System
    </div>

</body>
</html>
