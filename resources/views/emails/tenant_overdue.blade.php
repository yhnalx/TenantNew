<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h2 {
            color: #007bff;
        }
        .footer {
            margin-top: 25px;
            font-size: 13px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Payment Overdue Notice</h2>
    <p>Dear {{ $tenant->name }},</p>
    <p>Our records indicate that your rent or utility payment is currently <strong>overdue</strong>.</p>
    <p>Please settle your balance as soon as possible to avoid additional penalties or account restrictions.</p>
    <p>Thank you for your prompt attention to this matter.</p>
    <p>— Property Management</p>
    <div class="footer">
        <p>This is an automated message. Please do not reply.</p>
    </div>
</div>
</body>
</html>
