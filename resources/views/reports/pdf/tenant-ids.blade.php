<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tenant IDs</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            padding: 20px;
            text-align: center;
        }
        h2 { color: #0d6efd; }
        img {
            width: 300px;
            height: auto;
            margin: 15px;
            border: 2px solid #ccc;
            border-radius: 8px;
        }
        .id-section {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <h2>Tenant ID Verification</h2>
    <p><strong>Name:</strong> {{ $tenant->name }}</p>
    <p><strong>Email:</strong> {{ $tenant->email }}</p>

    <div class="id-section">
        <h3>Valid ID</h3>
        <img src="{{ public_path('storage/' . $validId) }}" alt="Valid ID">
    </div>

    <div class="id-section">
        <h3>ID Picture</h3>
        <img src="{{ public_path('storage/' . $idPicture) }}" alt="ID Picture">
    </div>
</body>
</html>
