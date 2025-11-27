<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - {{ $globalSettings->company_name ?? config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f8fafc;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            text-align: center;
            padding: 30px 20px;
        }
        .header i {
            font-size: 32px;
            color: white;
            background: rgba(255,255,255,0.15);
            padding: 16px;
            border-radius: 12px;
        }
        .header h1 {
            color: white;
            margin: 15px 0 5px;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            text-align: left;
        }
        .content h2 {
            font-size: 20px;
            color: #111827;
            margin-bottom: 10px;
        }
        .content p {
            font-size: 15px;
            color: #4b5563;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            margin-top: 25px;
        }
        .button:hover {
            background: #1e40af;
        }
        .footer {
            text-align: center;
            padding: 25px;
            background: #f1f5f9;
            font-size: 13px;
            color: #6b7280;
        }
        .footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container">
    <div class="header">
        <i class="fas fa-exchange-alt"></i>
        <h1>Reset Your Password</h1>
        <p style="color: rgba(255,255,255,0.8); font-size: 14px;">Secure access to your {{ ($globalSettings->company_name ?? config('app.name')) }} account</p>
    </div>

    <div class="content">
        <h2>Hello, {{ $user->first_name ?? 'User' }} 👋</h2>
        <p>We received a request to reset your password for your {{ ($globalSettings->company_name ?? config('app.name')) }} account. Click the button below to set a new password:</p>

        <a href="{{ $resetUrl }}" class="button">Reset Password</a>

        <p>If you didn’t request this, you can safely ignore this email. Your password won’t be changed until you click the button and set a new one.</p>

        <p style="margin-top: 25px;">Thanks,<br><strong>The {{ ($globalSettings->company_name ?? config('app.name')) }} Team</strong></p>
    </div>

    <div class="footer">
        <p>Need help? <a href="mailto:support@'{{ strtolower(config('app.name')) }}'.com">Contact Support</a></p>
        <p>&copy; {{ date('Y') }} {{ ($globalSettings->company_name ?? config('app.name')) }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
