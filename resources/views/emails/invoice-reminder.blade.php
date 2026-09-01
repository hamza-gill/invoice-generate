<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder - Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #d97706;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .content h1 {
            font-size: 22px;
            margin-bottom: 10px;
        }
        .invoice-summary {
            margin: 20px 0;
            padding: 20px;
            background-color: #f0f0f0;
            border-radius: 6px;
        }
        .invoice-summary p {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #d97706;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #999;
            background-color: #fafafa;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
@php
    $companyName = isset($globalSettings) && $globalSettings ? ($globalSettings->company_name ?? config('app.name')) : config('app.name');
    $currency = isset($globalSettings) && $globalSettings ? ($globalSettings->base_currency ?? '$') : '$';
@endphp
<div class="container">
    <div class="header">
        <h2>{{ $companyName }}</h2>
    </div>

    <div class="content">
        <h1>Friendly Payment Reminder</h1>
        <p>Hello {{ $invoice->customer->full_name ?? $invoice->customer->name ?? 'Customer' }},</p>
        <p>
            This is a reminder that your invoice
            <strong>#{{ $invoice->invoice_number }}</strong> is now
            <strong>{{ $overdueDays }} day(s) past due</strong>. We'd really appreciate it if you could settle it
            at your earliest convenience.
        </p>

        @php
            $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
            $rushFee = $invoice->rush_enabled_value ? $invoice->rush_fee : 0;
            $discount = $invoice->discount ?? 0;
            $total = $subtotal + $rushFee - $discount;
        @endphp

        <div class="invoice-summary">
            <h3>Invoice Summary</h3>
            <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
            <p><strong>Total Amount:</strong> {{ $currency }}{{ number_format($total, 2) }}</p>
        </div>

        <a href="{{ route('invoices.public', $invoice->id) }}" class="button">View &amp; Pay Invoice Online</a>

        <p style="margin-top: 20px;">A PDF copy of your invoice is attached for your reference.</p>
        <p style="margin-top: 20px;">If you've already made a payment, please disregard this message. Thank you!</p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
    </div>
</div>
</body>
</html>
