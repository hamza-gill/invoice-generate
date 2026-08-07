<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject ?? 'Test Email' }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; background: #f3f4f6; margin: 0; padding: 24px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 32px;">
        <h1 style="color: #1f2937; font-size: 20px; margin-top: 0;">Mail Configuration Successful</h1>
        <p style="color: #4b5563; font-size: 14px; line-height: 1.6;">
            This is a test email sent from <strong>{{ $companyName ?? config('app.name') }}</strong>.
            If you are reading this, your mail provider is configured correctly and invoices will be sent using it.
        </p>
        <p style="color: #9ca3af; font-size: 12px; margin-top: 24px;">
            Sent {{ now()->format('F j, Y g:i A') }}
        </p>
    </div>
</body>
</html>
