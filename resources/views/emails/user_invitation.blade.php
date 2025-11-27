<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invitation to {{($globalSettings->company_name ?? config('app.name'))}}</title>
</head>
<body style="font-family: Arial, sans-serif; color:#333;">
<h2>Hello,</h2>
<p>You have been invited to join <strong>{{ ($globalSettings->company_name ?? config('app.name'))  }}</strong>.</p>
<p>Click the button below to set up your account:</p>
<a href="{{ $url }}" style="background:#2563eb; color:white; padding:10px 20px; text-decoration:none; border-radius:6px;">
    Accept Invitation
</a>
<p>If you didn’t expect this invitation, you can safely ignore this email.</p>
</body>
</html>
