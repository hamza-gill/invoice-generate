@extends('layouts.guest.app')
@section('title', 'View Invoice - ReconX')
@section('content')
    <div class="max-w-5xl mx-auto mt-8 p-4 sm:p-6 bg-white rounded-2xl shadow-lg relative">

        {{-- COPY URL BUTTON (Top Right) --}}
        <button id="copyUrlBtn"
                class="absolute top-4 right-4 px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 flex items-center space-x-2 transition z-10">
            <i class="fas fa-link"></i>
            <span>Copy Link</span>
        </button>

        {{-- HEADER --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Review & Accept Invoice</h1>
            <p class="mt-1 text-gray-600 text-sm sm:text-base">Invoice #<strong>{{ $invoice->invoice_number }}</strong></p>
            <p class="mt-1 text-gray-700 font-semibold text-lg sm:text-xl">
                Amount Due: ${{ number_format($invoice->items->sum(fn($item) => $item->quantity * $item->amount), 2) }}
            </p>
        </div>

        {{-- CUSTOMER & INVOICE DETAILS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold mb-2">Customer Information</h2>
                <p class="text-sm sm:text-base"><strong>Name:</strong> {{ $invoice->customer->name }}</p>
                <p class="text-sm sm:text-base"><strong>Email:</strong> {{ $invoice->customer->email }}</p>
                <p class="text-sm sm:text-base"><strong>Address:</strong> {{ $invoice->project_address }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold mb-2">Invoice Details</h2>
                <p class="text-sm sm:text-base"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p class="text-sm sm:text-base"><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</p>
                <p class="text-sm sm:text-base"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- INVOICE ITEMS --}}
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
                </tbody>
                <tfoot class="bg-gray-100 font-semibold">
                <tr>
                    <td colspan="3" class="p-3 text-right">Subtotal</td>
                    <td class="p-3 text-right" id="subtotal-td">
                        ${{ number_format($invoice->items->sum(fn($item) => $item->quantity * $item->amount), 2) }}
                    </td>
                </tr>
                <tr id="rush-row" class="hidden">
                    <td colspan="3" class="p-3 text-right text-yellow-700">Rush Add-On</td>
                    <td class="p-3 text-right text-yellow-700" id="rush-total">$0.00</td>
                </tr>
                <tr>
                    <td colspan="3" class="p-3 text-right">Total</td>
                    <td class="p-3 text-right" id="total-td">
                        ${{ number_format($invoice->items->sum(fn($item) => $item->quantity * $item->amount), 2) }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        {{-- RUSH DELIVERY OPTIONS --}}
        @if ($invoice->rush_fee)
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold mb-2">Enable Rush Delivery</h3>
                <p class="text-gray-700 mb-3">Select your preferred delivery speed:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php
                        $rushOptions = [
                            ['days' => 2, 'fee' => 295],
                            ['days' => 3, 'fee' => 195],
                            ['days' => 4, 'fee' => 95],
                            ['days' => 'standard', 'fee' => 0],
                        ];
                    @endphp
                    @foreach ($rushOptions as $option)
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-yellow-100 transition">
                            <input type="radio" name="rush_option" value="{{ $option['days'] }}" data-fee="{{ $option['fee'] }}" class="rush-option-radio mr-3"
                                   @if($option['days'] === 'standard') checked @endif>
                            <span class="font-medium text-gray-800 text-sm sm:text-base">
                            @if($option['days'] === 'standard')
                                    Standard Timing: Delivery within 5-7 business days (FREE)
                                @else
                                    Delivery within {{ $option['days'] }} business day{{ $option['days'] > 1 ? 's' : '' }} + ${{ $option['fee'] }}
                                @endif
                        </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PAYMENT FORM --}}
        <form id="payment-form" action="{{ route('invoices.pay', $invoice->id) }}" method="POST">
            @csrf
            <input type="hidden" name="rush_enabled_value" id="rush_enabled_value" value="0">
            <input type="hidden" name="rush_delivery_days" id="rush_delivery_days" value="standard">

            <button type="submit"
                    class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition">
                Proceed to Secure Payment
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // COPY URL BUTTON
            document.getElementById('copyUrlBtn').addEventListener('click', function () {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Link copied!',
                        text: 'Invoice URL copied to clipboard',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                }).catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Copy failed',
                        text: 'Unable to copy the link.',
                    });
                });
            });

            // RUSH DELIVERY OPTIONS
            const rushRadios = document.querySelectorAll('.rush-option-radio');
            const rushRow = document.getElementById('rush-row');
            const rushTotalEl = document.getElementById('rush-total');
            const totalEl = document.getElementById('total-td');
            const subtotal = parseFloat(@json($invoice->items->sum(fn($i) => $i->quantity * $i->amount)));
            const rushValueInput = document.getElementById('rush_enabled_value');
            const rushDaysInput = document.getElementById('rush_delivery_days');

            function updateRushTotal(fee, days) {
                if (fee > 0) {
                    rushRow.classList.remove('hidden');
                    rushTotalEl.textContent = '$' + fee.toFixed(2);
                } else {
                    rushRow.classList.add('hidden');
                    rushTotalEl.textContent = '$0.00';
                }
                totalEl.textContent = '$' + (subtotal + fee).toFixed(2);
                rushValueInput.value = fee > 0 ? 1 : 0;
                rushDaysInput.value = days;
            }

            rushRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const fee = parseFloat(this.dataset.fee);
                    const days = this.value;
                    updateRushTotal(fee, days);
                });

                if (radio.checked) {
                    updateRushTotal(parseFloat(radio.dataset.fee), radio.value);
                }
            });
        });
    </script>
@endsection
