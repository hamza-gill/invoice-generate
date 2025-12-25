<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        /* === PAGE SETUP === */
        @page {
            size: A4;
            margin: 20mm 20mm 20mm 20mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px 25px 20px 25px;
        }

        /* === HEADER === */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #d1d5db;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .header-left { width: 60%; vertical-align: top; }
        .header-right { width: 40%; text-align: right; vertical-align: top; }
        .logo { max-height: 45px; margin-bottom: 8px; }
        .header-table h1 { font-size: 26px; margin: 0 0 4px 0; color: #111827; font-weight: 700; }
        .header-table p { margin: 2px 0; color: #4b5563; }

        /* === BILLING === */
        .billing { margin-bottom: 20px; page-break-inside: avoid; }
        .billing table { width: 100%; border-collapse: collapse; }
        .billing td { vertical-align: top; padding: 3px 0; }

        /* === ITEMS TABLE === */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        table.items th, table.items td {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 6px;
            font-size: 13px;
        }

        table.items th {
            background-color: #f9fafb;
            color: #374151;
            font-weight: 600;
        }

        table.items td:last-child { text-align: right; }

        /* === MULTIPAGE TABLE SUPPORT === */
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }
        tr { page-break-inside: avoid; }

        /* === TOTALS === */
        .totals {
            width: 100%;
            margin-top: 15px;
            clear: both;
            page-break-inside: avoid;
        }
        .totals-inner {
            float: right;
            width: 250px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #e5e7eb;
            padding: 5px 0;
        }
        .totals-row.total {
            border-top: 2px solid #111827;
            font-weight: 700;
            font-size: 15px;
            margin-top: 5px;
        }

        /* === STATUS === */
        .status {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            border-radius: 8px;
            font-weight: 600;
        }
        .status.sent { background-color: #dbeafe; color: #1e3a8a; }
        .status.paid { background-color: #dcfce7; color: #166534; }
        .status.void { background-color: #fee2e2; color: #991b1b; }
        .status.default { background-color: #e5e7eb; color: #374151; }

        /* === NOTES === */
        .notes {
            clear: both;
            border-top: 1px solid #e5e7eb;
            margin-top: 40px;
            padding-top: 15px;
            page-break-inside: avoid;
        }
        .notes h3 {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 6px;
        }
        .notes p {
            font-size: 13px;
            color: #4b5563;
            margin-bottom: 10px;
            white-space: pre-line;
        }

    </style>
</head>
<body>
<div class="container">

    {{-- HEADER --}}
    <table class="header-table">
        <tr>
            <td class="header-left">
                <h1>INVOICE</h1>
                <p>#{{ $invoice->invoice_number }}</p>
                @if(!empty($invoice->project_address))
                    <p style="margin-top:10px;font-weight:600;">PROJECT ADDRESS:</p>
                    <p>{{ $invoice->project_address }}</p>
                @endif
            </td>
            <td class="header-right">
                @if(!empty($globalSettings->logo_path))
                    <img src="{{ asset('storage/' . $globalSettings->logo_path) }}" class="logo" alt="Logo">
                @endif
                <p><strong>{{ $globalSettings->company_name ?? config('app.name') }}</strong></p>
                <p>{{ $globalSettings->address ?? '' }}</p>
                @if($globalSettings->enable_tax_id && !empty($globalSettings->tax_id))
                    <p>Tax ID: {{ $globalSettings->tax_id }}</p>
                @endif
                @if($globalSettings->contact_email)
                    <p>Email: {{ $globalSettings->contact_email }}</p>
                @endif
            </td>
        </tr>
    </table>

    {{-- BILLING --}}
    <div class="billing">
        <table>
            <tr>
                <td>
                    <p><strong>BILL TO:</strong></p>
                    <p>{{ $invoice->customer->full_name ?? 'N/A' }}</p>
                    <p>{{ $invoice->customer->email ?? 'N/A' }}</p>
                    <p>{{ $invoice->customer->address ?? 'N/A' }}</p>
                </td>
                <td style="text-align:right;">
                    <table>
                        <tr>
                            <td><strong>Issue Date:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</td>
                        </tr>
                        @if(!empty($globalSettings->enable_due_date) && $globalSettings->enable_due_date)
                            <tr>
                                <td><strong>Due Date:</strong></td>
                                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @php
                                    $statusClass = match($invoice->status) {
                                        'sent' => 'sent',
                                        'paid' => 'paid',
                                        'void' => 'void',
                                        default => 'default'
                                    };
                                @endphp
                                <span class="status {{ $statusClass }}">{{ strtoupper($invoice->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ITEMS --}}
    @php
        $currency = $globalSettings->base_currency ?? '$';
        $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
        $rushFee = $invoice->rush_enabled_value ? ($invoice->rush_fee ?? 0) : 0;

        $discount = $invoice->discount ?? 0;

        $taxRate = (!empty($globalSettings->enable_tax) && $globalSettings->enable_tax)
                    ? ($globalSettings->tax_percentage ?? 0) / 100
                    : 0;

        // Tax is calculated BEFORE discount (standard)
        $taxAmount = ($subtotal + $rushFee) * $taxRate;

        // Final total (discount applied at end)
        $total = max(0, $subtotal + $rushFee + $taxAmount - $discount);
    @endphp

    <table class="items">
        <thead>
        <tr>
            <th>SERVICE</th>
            <th>DESCRIPTION</th>
            <th style="text-align:right;">AMOUNT</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->description }}</td>
                <td>{{ $currency }}{{ number_format($item->quantity * $item->amount, 2) }}</td>
            </tr>
        @endforeach

        {{-- Rush Add-On --}}
        @if($invoice->rush_enabled_value)
            <tr class="bg-yellow-50 font-semibold">
                <td>Rush Add-On ({{ ucfirst($invoice->rush_delivery_type) ?? 'Fast' }})</td>
                <td>Expedite delivery service</td>
                <td style="text-align:right">{{ $currency }}{{ number_format($rushFee, 2) }}</td>
            </tr>
        @endif
        </tbody>
    </table>

    {{-- TOTALS --}}
    <div class="totals">
        <div class="totals-inner">
            <div class="totals-row">
                <span><strong>Subtotal:</strong></span>
                <span>{{ $currency }}{{ number_format($subtotal, 2) }}</span>
            </div>

            @if($invoice->rush_enabled_value)
                <div class="totals-row font-semibold">
                    <span>Rush Add-On ({{ ucfirst($invoice->rush_delivery_type) }}):</span>
                    <span>{{ $currency }}{{ number_format($rushFee, 2) }}</span>
                </div>
            @endif

            @if($globalSettings->enable_tax)
                <div class="totals-row">
                    <span><strong>Tax ({{ $globalSettings->tax_percentage }}%):</strong></span>
                    <span>{{ $currency }}{{ number_format($taxAmount, 2) }}</span>
                </div>
            @endif

            @if($discount > 0)
                <div class="totals-row" style="color:#b91c1c;font-weight:600;">
                    <span>Discount:</span>
                    <span>-{{ $currency }}{{ number_format($discount, 2) }}</span>
                </div>
            @endif

            <div class="totals-row total">
                <span>Total:</span>
                <span>{{ $currency }}{{ number_format($total, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- NOTES --}}
    <div class="notes">
        @if(!empty($invoice->note))
            <h3>Project Notes</h3>
            <p>{{ $invoice->note }}</p>
        @endif
        @if($globalSettings->enable_invoice_notes && !empty($globalSettings->invoice_notes))
            <h3>Notes</h3>
            <p>{{ $globalSettings->invoice_notes }}</p>
        @endif
        @if($globalSettings->enable_terms && !empty($globalSettings->invoice_terms))
            <h3>Terms & Conditions</h3>
            <p>{{ $globalSettings->invoice_terms }}</p>
        @endif
    </div>

</div>
</body>
</html>
