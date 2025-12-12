<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Rejected - #{{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans">

<div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg">

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-red-600 flex justify-center items-center gap-2 mb-2">
            ❌ Invoice #{{ $invoice->invoice_number }} Rejected
        </h1>
        <p class="text-gray-700 mb-3">This invoice has been rejected by the recipient.</p>
        <span class="inline-block px-3 py-1 rounded text-white bg-red-600 font-semibold">Rejected</span>
    </div>

    {{-- Customer & Invoice Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-2">Billed To:</h2>
            <p class="text-sm sm:text-base"><strong>Name:</strong> {{ $invoice->customer->name ?? 'N/A' }}</p>
            <p class="text-sm sm:text-base"><strong>Email:</strong> {{ $invoice->customer->email ?? 'N/A' }}</p>
            <p class="text-sm sm:text-base"><strong>Address:</strong> {{ $invoice->customer->address ?? 'N/A' }}</p>
        </div>
        <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-2">Invoice Details:</h2>
            <p class="text-sm sm:text-base"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
            <p class="text-sm sm:text-base"><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</p>
            <p class="text-sm sm:text-base"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
            <p class="text-sm sm:text-base mt-1">
                <strong>Status:</strong>
                <span class="px-2 py-1 rounded font-semibold text-white bg-red-600">Rejected</span>
            </p>
        </div>
    </div>

    {{-- Invoice Items --}}
    <div class="overflow-x-auto mb-6">
        <table class="min-w-full border-collapse border rounded-lg overflow-hidden text-sm sm:text-base">
            <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">Description</th>
                <th class="p-3 text-center">Qty</th>
                <th class="p-3 text-right">Unit Price</th>
                <th class="p-3 text-right">Total</th>
            </tr>
            </thead>
            <tbody class="bg-white">
            @foreach ($invoice->items as $item)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-3">{{ $item->activity }}</td>
                    <td class="p-3 text-center">{{ $item->quantity }}</td>
                    <td class="p-3 text-right">${{ number_format($item->amount, 2) }}</td>
                    <td class="p-3 text-right">${{ number_format($item->quantity * $item->amount, 2) }}</td>
                </tr>
            @endforeach
            @if ($invoice->rush_enabled_value)
                <tr class="bg-yellow-50">
                    <td class="p-3 font-medium">Rush Add-On ({{ ucfirst($invoice->rush_delivery_type) }})</td>
                    <td class="p-3 text-center">1</td>
                    <td class="p-3 text-right">${{ number_format($invoice->rush_fee, 2) }}</td>
                    <td class="p-3 text-right">${{ number_format($invoice->rush_fee, 2) }}</td>
                </tr>
            @endif
            </tbody>
            @php
                $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
                $rushFee = ($invoice->rush_enabled_value) ? $invoice->rush_fee : 0;
                $discount = $invoice->discount ?? 0;
                $total = $subtotal + $rushFee - $discount;
            @endphp
            <tfoot class="bg-gray-100 font-semibold">
            <tr>
                <td colspan="3" class="p-3 text-right">Subtotal:</td>
                <td class="p-3 text-right">${{ number_format($subtotal + $rushFee, 2) }}</td>
            </tr>
            @if($discount > 0)
                <tr>
                    <td colspan="3" class="p-3 text-right text-red-600">Discount:</td>
                    <td class="p-3 text-right text-red-600">-${{ number_format($discount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td colspan="3" class="p-3 text-right">Total:</td>
                <td class="p-3 text-right">${{ number_format($total, 2) }}</td>
            </tr>
            </tfoot>
        </table>
    </div>

    {{-- Rejection Reason --}}
    @if(!empty($invoice->rejection_reason))
        <div class="bg-red-50 p-4 rounded-lg mb-6 border border-red-200">
            <h3 class="font-semibold text-lg mb-1 text-red-700">Reason for Rejection:</h3>
            <p class="text-red-700 text-sm sm:text-base">{{ $invoice->rejection_reason }}</p>
        </div>
    @endif

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row justify-center gap-3">
        <a href="{{ route('invoices.show', $invoice->id) }}"
           class="bg-indigo-600 text-white font-semibold py-2 px-6 rounded-xl hover:bg-indigo-700 transition text-center w-full sm:w-auto">
            View Invoice
        </a>
        <a href="mailto:support@yourcompany.com"
           class="bg-gray-200 text-gray-800 font-semibold py-2 px-6 rounded-xl hover:bg-gray-300 transition text-center w-full sm:w-auto">
            Contact Support
        </a>
    </div>

</div>
</body>
</html>
