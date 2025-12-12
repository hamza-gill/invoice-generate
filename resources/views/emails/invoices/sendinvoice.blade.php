<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
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
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
        }
        .content h1 {
            font-size: 24px;
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
            background-color: #4a90e2;
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
        .notes, .terms {
            margin-top: 25px;
            font-size: 14px;
            line-height: 1.5;
            color: #555;
        }
        .notes h3, .terms h3 {
            color: #333;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        @if(!empty($globalSettings->logo_path))
            <img src="{{ asset($globalSettings->logo_path) }}" alt="Logo" style="height: 50px; margin-bottom: 10px;">
        @endif
        <h2>{{ $globalSettings->company_name ?? config('app.name') }}</h2>
    </div>

    <div class="content">
        <h1>Hello {{ $invoice->customer->name ?? 'Customer' }},</h1>
        <p>We’ve generated your invoice <strong>#{{ $invoice->invoice_number }}</strong>.</p>

        @php
            $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
            $rushFee = $invoice->rush_enabled_value ? $invoice->rush_fee : 0;
            $discount = $invoice->discount ?? 0;
            $total = $subtotal + $rushFee - $discount;
        @endphp

        <div class="invoice-summary">
            <h3>Invoice Summary</h3>
            <p><strong>Subtotal:</strong> {{ $globalSettings->base_currency ?? '$' }}{{ number_format($subtotal + $rushFee, 2) }}</p>
            @if($discount > 0)
                <p><strong>Discount:</strong> -{{ $globalSettings->base_currency ?? '$' }}{{ number_format($discount, 2) }}</p>
            @endif
            <p><strong>Total Amount:</strong> {{ $globalSettings->base_currency ?? '$' }}{{ number_format($total, 2) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
            <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
        </div>

        <a href="{{ route('invoices.show', $invoice->id) }}" class="button">View Invoice Online</a>

        <p style="margin-top: 20px;">A PDF copy of your invoice is attached for your convenience.</p>

        {{-- Notes --}}
        @if(!empty($globalSettings->invoice_notes))
            <div class="notes">
                <h3>Notes</h3>
                <p>{{ $globalSettings->invoice_notes }}</p>
            </div>
        @endif

        {{-- Terms & Conditions --}}
        @if(!empty($globalSettings->invoice_terms))
            <div class="terms">
                <h3>Terms & Conditions</h3>
                <p>{{ $globalSettings->invoice_terms }}</p>
            </div>
        @endif

        <p style="margin-top: 25px;">Thanks for your business!<br>
            <strong>{{ $globalSettings->company_name ?? config('app.name') }}</strong><br>
            @if(!empty($globalSettings->address))
                {{ $globalSettings->address }}<br>
            @endif
            @if(!empty($globalSettings->contact_email))
                <a href="mailto:{{ $globalSettings->contact_email }}">{{ $globalSettings->contact_email }}</a>
            @endif
        </p>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} {{ $globalSettings->company_name ?? config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
