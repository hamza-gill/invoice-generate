@extends('layouts.guest.app')
@section('title', 'View Invoice -'.($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="max-w-5xl mx-auto mt-10 p-4 sm:p-6 bg-white rounded-3xl shadow-lg">

        {{-- 🔗 Top Right Actions --}}
        <div class="flex flex-col sm:flex-row justify-end gap-3 mb-6">
            <button id="copyUrlBtn"
                    class="flex items-center gap-2 border border-indigo-600 text-indigo-600 px-4 py-2 rounded-xl hover:bg-indigo-50 transition">
                📋 Copy URL
            </button>

            @if($invoice->status === 'paid')
                <a href="{{ route('invoices.download', $invoice->id) }}"
                   class="bg-green-600 text-white font-semibold py-2 px-6 rounded-xl hover:bg-green-700 transition text-center">
                    Download Invoice PDF
                </a>
            @else
                <a href="{{ route('invoices.accept.page', $invoice->id) }}"
                   class="bg-indigo-600 text-white font-semibold py-2 px-6 rounded-xl hover:bg-indigo-700 transition text-center">
                    Pay Invoice Now
                </a>
            @endif
        </div>

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-indigo-600 mb-2">Invoice #{{ $invoice->invoice_number }}</h1>
            <p class="text-gray-700 text-lg sm:text-xl">Amount Due: <strong>${{ number_format($invoice->amount, 2) }}</strong></p>
            <p class="mt-2">
            <span class="px-4 py-1 rounded-full text-white font-semibold text-sm
                {{ $invoice->status === 'paid' ? 'bg-green-600' : ($invoice->status === 'void' ? 'bg-red-600' : 'bg-gray-500') }}">
                {{ ucfirst($invoice->status) }}
            </span>
            </p>
        </div>

        {{-- Invoice & Customer Details --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="p-4 bg-gray-50 rounded-xl shadow-sm">
                <h2 class="font-semibold text-lg mb-2">Billed To:</h2>
                <p>{{ $invoice->customer->name ?? 'N/A' }}</p>
                <p>{{ $invoice->customer->email ?? 'N/A' }}</p>
                <p>{{ $invoice->customer->address ?? 'N/A' }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-xl shadow-sm">
                <h2 class="font-semibold text-lg mb-2">Invoice Info:</h2>
                <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</p>
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Invoice Items Table --}}
        <div class="overflow-x-auto shadow rounded-xl mb-6">
            <table class="min-w-full border-collapse border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border p-3 text-left text-sm sm:text-base">Description</th>
                    <th class="border p-3 text-center text-sm sm:text-base">Qty</th>
                    <th class="border p-3 text-right text-sm sm:text-base">Unit Price</th>
                    <th class="border p-3 text-right text-sm sm:text-base">Total</th>
                </tr>
                </thead>
                <tbody class="bg-white">
                @foreach ($invoice->items as $item)
                    <tr class="border-b hover:bg-gray-50 transition">
                        <td class="p-3 text-sm sm:text-base">{{ $item->activity }}</td>
                        <td class="p-3 text-center text-sm sm:text-base">{{ $item->quantity }}</td>
                        <td class="p-3 text-right text-sm sm:text-base">${{ number_format($item->amount, 2) }}</td>
                        <td class="p-3 text-right text-sm sm:text-base">${{ number_format($item->quantity * $item->amount, 2) }}</td>
                    </tr>
                @endforeach
                @if ($invoice->rush_enabled_value)
                    <tr class="bg-yellow-50">
                        <td class="p-3 font-medium text-sm sm:text-base">Rush Add-On ({{ ucfirst($invoice->rush_delivery_type) }})</td>
                        <td class="p-3 text-center text-sm sm:text-base">1</td>
                        <td class="p-3 text-right text-sm sm:text-base">${{ number_format($invoice->rush_fee, 2) }}</td>
                        <td class="p-3 text-right text-sm sm:text-base">${{ number_format($invoice->rush_fee, 2) }}</td>
                    </tr>
                @endif
                </tbody>
                @php
                    $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
                    $rushFee = ($invoice->rush_enabled_value) ? $invoice->rush_fee : 0;
                    $total = $subtotal + $rushFee;
                @endphp
                <tfoot class="bg-gray-100 font-semibold text-sm sm:text-base">
                <tr>
                    <td colspan="3" class="p-3 text-right">Subtotal:</td>
                    <td class="p-3 text-right">${{ number_format($subtotal + $rushFee, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="p-3 text-right">Total:</td>
                    <td class="p-3 text-right">${{ number_format($total, 2) }}</td>
                </tr>
                </tfoot>
            </table>
        </div>

        {{-- Notes --}}
        @if(!empty($invoice->note))
            <div class="bg-gray-50 p-4 rounded-xl mb-6 shadow-sm">
                <h3 class="font-semibold text-lg mb-1">Notes</h3>
                <p class="text-gray-700">{{ $invoice->note }}</p>
            </div>
        @endif
    </div>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Copy URL --}}
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
