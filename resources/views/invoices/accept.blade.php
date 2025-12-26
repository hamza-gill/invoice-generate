@extends('layouts.guest.app')
@section('title', 'View Invoice - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="max-w-5xl mx-auto mt-8 p-4 sm:p-6 bg-white rounded-2xl shadow-lg relative">

        {{-- COPY URL BUTTON --}}
        <button id="copyUrlBtn"
                class="absolute top-4 right-4 px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 flex items-center space-x-2 transition z-10">
            <i class="fas fa-link"></i>
            <span>Copy Link</span>
        </button>

        @php
            $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
            $rushFee = $invoice->rush_fee ?? 0;
            $discount = $invoice->discount ?? 0;

            $taxAmount = ($globalSettings->enable_tax ?? false)
                ? (($subtotal + $rushFee) * (($globalSettings->tax_percentage ?? 0) / 100))
                : 0;

            $grandTotal = max(0, $subtotal + $rushFee + $taxAmount - $discount);
        @endphp

        {{-- HEADER --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Review & Accept Invoice</h1>
            <p class="mt-1 text-gray-600 text-sm sm:text-base">
                Invoice #<strong>{{ $invoice->invoice_number }}</strong>
            </p>
            <p class="mt-1 text-gray-700 font-semibold text-lg sm:text-xl">
                Amount Due: ${{ number_format($grandTotal, 2) }}
            </p>
        </div>

        {{-- CUSTOMER & INVOICE DETAILS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold mb-2">Customer Information</h2>
                <p><strong>Name:</strong> {{ $invoice->customer->full_name }}</p>
                <p><strong>Email:</strong> {{ $invoice->customer->email }}</p>
                <p><strong>Address:</strong> {{ $invoice->project_address }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold mb-2">Invoice Details</h2>
                <p><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Issue Date:</strong> {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</p>
                <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- INVOICE ITEMS --}}
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full border-collapse border rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-center">Qty</th>
                    <th class="p-3 text-right">Unit Price</th>
                    <th class="p-3 text-right">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($invoice->items as $item)
                    <tr class="border-b">
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
                        ${{ number_format($subtotal, 2) }}
                    </td>
                </tr>

                <tr id="rush-row" class="hidden">
                    <td colspan="3" class="p-3 text-right text-yellow-700">Rush Add-On</td>
                    <td class="p-3 text-right text-yellow-700" id="rush-total">$0.00</td>
                </tr>

                <tr id="discount-row" class="@if(!$discount) hidden @endif">
                    <td colspan="3" class="p-3 text-right text-red-600">Discount</td>
                    <td class="p-3 text-right text-red-600">
                        ${{ number_format($discount, 2) }}
                    </td>
                </tr>

                @if($globalSettings->enable_tax)
                    <tr>
                        <td colspan="3" class="p-3 text-right">
                            Tax ({{ $globalSettings->tax_percentage ?? 0 }}%)
                        </td>
                        <td class="p-3 text-right" id="tax-td">
                            ${{ number_format($taxAmount, 2) }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td colspan="3" class="p-3 text-right">Total</td>
                    <td class="p-3 text-right" id="total-td">
                        ${{ number_format($grandTotal, 2) }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        {{-- RUSH DELIVERY OPTIONS — UNTOUCHED --}}
        @if ($globalSettings->enable_rush_delivery)
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold mb-2">Enable Rush Delivery</h3>
                <p class="text-gray-700 mb-3">Select your preferred delivery speed:</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($globalSettings->rush_options as $option)
                        @php
                            $today = \Carbon\Carbon::today();
                            $deliveryDate = $today->copy();
                            $addedDays = 0;

                            while ($addedDays < ($option['days'] === 'standard' ? 7 : $option['days'])) {
                                $deliveryDate->addDay();
                                if (!$deliveryDate->isWeekend()) $addedDays++;
                            }
                        @endphp

                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-yellow-100 transition">
                            <input type="radio"
                                   name="rush_option"
                                   value="{{ $option['days'] }}"
                                   data-fee="{{ $option['fee'] }}"
                                   data-label="{{ $option['label'] }}"
                                   class="rush-option-radio mr-3"
                                   @if($option['days'] === 'standard') checked @endif>

                            <span class="font-medium">
                            {{ $option['label'] }}
                                @if($option['fee'] > 0)
                                    + ${{ number_format($option['fee'], 2) }}
                                @else
                                    (FREE)
                                @endif
                            <br>
                            <span class="text-xs text-gray-600">
                                Delivery by: {{ $deliveryDate->format('M d, Y') }}
                            </span>
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
            <input type="hidden" name="rush_label" id="rush_label" value="">
            <input type="hidden" name="rush_fee" id="rush_fee" value="0">

            <button type="submit"
                    class="w-full bg-indigo-600 text-white font-semibold py-3 rounded-xl hover:bg-indigo-700 transition">
                Proceed to Secure Payment
            </button>
        </form>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            document.getElementById('copyUrlBtn').addEventListener('click', function () {
                navigator.clipboard.writeText(window.location.href);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Link copied!',
                    showConfirmButton: false,
                    timer: 2000,
                });
            });

            const rushRadios = document.querySelectorAll('.rush-option-radio');
            const rushRow = document.getElementById('rush-row');
            const rushTotalEl = document.getElementById('rush-total');
            const totalEl = document.getElementById('total-td');
            const taxTd = document.getElementById('tax-td');

            const subtotal = parseFloat(@json($subtotal));
            const discount = parseFloat(@json($discount));
            const taxRate = @json($globalSettings->enable_tax ? (($globalSettings->tax_percentage ?? 0) / 100) : 0);

            function updateTotals(fee, days, label) {
                if (fee > 0) {
                    rushRow.classList.remove('hidden');
                    rushTotalEl.textContent = '$' + fee.toFixed(2);
                } else {
                    rushRow.classList.add('hidden');
                }

                const tax = (subtotal + fee) * taxRate;
                if (taxTd) taxTd.textContent = '$' + tax.toFixed(2);

                const total = subtotal + fee + tax - discount;
                totalEl.textContent = '$' + total.toFixed(2);

                document.getElementById('rush_enabled_value').value = fee > 0 ? 1 : 0;
                document.getElementById('rush_delivery_days').value = days;
                document.getElementById('rush_label').value = label;
                document.getElementById('rush_fee').value = fee.toFixed(2);
            }

            rushRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    updateTotals(parseFloat(this.dataset.fee), this.value, this.dataset.label);
                });

                if (radio.checked) {
                    updateTotals(parseFloat(radio.dataset.fee), radio.value, radio.dataset.label);
                }
            });
        });
    </script>
@endsection
