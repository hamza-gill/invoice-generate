@extends('layouts.guest.app')
@section('title', 'View Invoice -'.($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="max-w-5xl mx-auto mt-8 p-4 sm:p-6 bg-white rounded-2xl shadow-lg relative">

        {{-- 🔗 Top Right Buttons --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 mb-5">
            {{-- Copy URL --}}
            <button id="copyUrlBtn"
                    class="flex items-center gap-2 border border-indigo-600 text-indigo-600 px-4 py-2 rounded-xl hover:bg-indigo-50 transition w-full sm:w-auto justify-center">
                📋 Copy URL
            </button>

            {{-- Download PDF --}}
            <a href="{{ route('invoices.download', $invoice->id) }}"
               class="bg-indigo-600 text-white font-semibold py-2 px-6 rounded-xl hover:bg-indigo-700 transition w-full sm:w-auto text-center">
                Download PDF
            </a>
        </div>

        {{-- Success Header --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-green-600 mb-2 flex items-center justify-center gap-2">
                ✅ Payment Successful
            </h1>
            <p class="text-gray-700 text-sm sm:text-base">Thank you for paying Invoice <strong>#{{ $invoice->invoice_number }}</strong>.</p>
            <p class="text-gray-700 text-sm sm:text-base mt-1 font-semibold">
                Amount Paid: <strong>${{ number_format($invoice->amount, 2) }}</strong>
            </p>
        </div>

        {{-- Invoice Details --}}
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                    <h2 class="text-lg font-semibold mb-2">Billed To:</h2>
                    <p class="text-sm sm:text-base">{{ $invoice->customer->name ?? 'N/A' }}</p>
                    <p class="text-sm sm:text-base">{{ $invoice->customer->email ?? 'N/A' }}</p>
                    <p class="text-sm sm:text-base">{{ $invoice->customer->address ?? 'N/A' }}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                    <h2 class="text-lg font-semibold mb-2">Invoice Info:</h2>
                    <p class="text-sm sm:text-base"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                    <p class="text-sm sm:text-base"><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</p>
                    <p class="text-sm sm:text-base"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
                    <p class="text-sm sm:text-base mt-1">
                        <strong>Status:</strong>
                        <span class="px-2 py-1 rounded font-semibold text-white {{ $invoice->status == 'paid' ? 'bg-green-600' : 'bg-gray-400' }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    </p>
                </div>
            </div>

            {{-- Invoice Items --}}
            <div class="overflow-x-auto">
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
                        <tr class="border-t bg-yellow-50">
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
                        <td colspan="3" class="p-3 text-right">Total Paid:</td>
                        <td class="p-3 text-right">${{ number_format($total, 2) }}</td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Notes --}}
            @if(!empty($invoice->note))
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-lg mb-1">Notes</h3>
                    <p class="text-gray-700 text-sm sm:text-base">{{ $invoice->note }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- SweetAlert Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('copyUrlBtn').addEventListener('click', function () {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'URL Copied!',
                    text: 'Invoice link has been copied.',
                    showConfirmButton: false,
                    timer: 1800,
                    timerProgressBar: true,
                });
            }).catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Copy Failed',
                    text: 'Unable to copy URL. Try again.',
                });
            });
        });
    </script>
@endsection
