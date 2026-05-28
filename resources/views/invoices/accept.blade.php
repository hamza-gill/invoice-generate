@extends('layouts.guest.app')
@section('title', 'View Invoice - ' . ($globalSettings->company_name ?? config('app.name')))
@section('body-class', 'template-themed')
@section('theme-styles')
    @include('invoices.partials.template-theme-styles', ['templateTheme' => $templateTheme ?? []])
@endsection

@section('content')
    @php
        $currency = $globalSettings->base_currency ?? '$';
        $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
        $rushFee = $invoice->rush_fee ?? 0;
        $discount = $invoice->discount ?? 0;

        $taxAmount = ($globalSettings->enable_tax ?? false)
            ? (($subtotal + $rushFee) * (($globalSettings->tax_percentage ?? 0) / 100))
            : 0;

        $grandTotal = max(0, $subtotal + $rushFee + $taxAmount - $discount);
        $dueLabel = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : null;
    @endphp

    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="flex justify-center mb-4">
                <span class="inline-flex items-center gap-2 rounded-full border bg-white/60 px-3 py-1 text-xs text-slate-600 shadow-sm backdrop-blur" style="border-color: var(--theme-card-border);">
                    <i class="fas fa-shield-alt theme-icon"></i>
                    Secure invoice · Encrypted end-to-end
                    @if(!empty($templateTheme['name']))
                        <span class="text-slate-400">·</span>
                        <span class="font-medium theme-accent-text">{{ $templateTheme['name'] }}</span>
                    @endif
                </span>
            </div>

            <div class="rounded-3xl border bg-white/70 backdrop-blur overflow-hidden" style="border-color: var(--theme-card-border); box-shadow: var(--theme-card-shadow);">
                <div class="relative px-8 py-7" style="background: var(--theme-header); color: var(--theme-header-text);">
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35), transparent 40%), radial-gradient(circle at 85% 0%, rgba(255,255,255,0.25), transparent 45%);"></div>

                    <div class="relative flex items-start justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold tracking-widest uppercase theme-header-text-muted">Invoice {{ $invoice->invoice_number }}</div>
                            <h1 class="mt-1 text-2xl sm:text-3xl font-bold theme-header-text">Review &amp; Accept Invoice</h1>
                            <p class="mt-1 text-sm theme-header-text-muted">Please review the details below. Your payment is protected by bank-grade encryption.</p>
                        </div>
                        <button id="copyUrlBtn" class="relative inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold ring-1 transition theme-header-text" style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.22);">
                            <i class="fas fa-link"></i>
                            Copy Link
                        </button>
                    </div>

                    <div class="relative mt-6 rounded-2xl p-5 sm:p-6 ring-1" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.18);">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="text-[11px] font-semibold tracking-widest uppercase theme-header-text-muted">Amount Due</div>
                                <div id="header-amount-due" class="mt-1 text-3xl sm:text-4xl font-extrabold theme-header-text">{{ $currency }}{{ number_format($grandTotal, 2) }}</div>
                            </div>
                            @if($dueLabel)
                                <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ring-1" style="background: var(--theme-badge-bg); color: var(--theme-badge-text); border-color: var(--theme-badge-ring);">
                                    <i class="fas fa-calendar"></i>
                                    Due {{ $dueLabel }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-4 sm:p-6 bg-white">
                    @if(!empty($invoiceDocumentSrcdoc))
                        @include('invoices.partials.template-document', [
                            'invoiceDocumentSrcdoc' => $invoiceDocumentSrcdoc,
                            'templateName' => $invoiceTemplate?->name,
                            'invoiceNumber' => $invoice->invoice_number,
                        ])
                    @else
                    <div class="p-2 sm:p-4">
                    <div class="mt-2 overflow-x-auto rounded-2xl border border-slate-100">
                        <table class="min-w-full">
                            <thead class="bg-slate-50">
                                <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                                    <th class="px-5 py-4 text-left">Description</th>
                                    <th class="px-5 py-4 text-center">Qty</th>
                                    <th class="px-5 py-4 text-right">Unit Price</th>
                                    <th class="px-5 py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td class="px-5 py-4 text-sm text-slate-800">{{ $item->activity }}</td>
                                        <td class="px-5 py-4 text-sm text-center text-slate-600">{{ $item->quantity }}</td>
                                        <td class="px-5 py-4 text-sm text-right text-slate-600">{{ $currency }}{{ number_format($item->amount, 2) }}</td>
                                        <td class="px-5 py-4 text-sm text-right font-semibold text-slate-800">{{ $currency }}{{ number_format($item->quantity * $item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="px-5 py-5 text-base text-right font-semibold text-slate-700">Total</td>
                                    <td class="px-5 py-5 text-base text-right font-extrabold theme-accent-text" id="total-td">{{ $currency }}{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    @endif

                    @if ($globalSettings->enable_rush_delivery)
                        <div class="mt-6 rounded-2xl border p-5" style="border-color: var(--theme-badge-ring); background: var(--theme-badge-bg);">
                            <div class="flex items-center gap-2 text-sm font-semibold theme-accent-text">
                                <i class="fas fa-shipping-fast"></i>
                                Rush Delivery
                            </div>
                            <p class="mt-1 text-sm text-slate-600">Select your preferred delivery speed.</p>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($globalSettings->rush_options as $option)
                                    @php
                                        $today = \Carbon\Carbon::today();
                                        $deliveryDate = $today->copy();
                                        $addedDays = 0;
                                        while ($addedDays < ($option['days'] === 'standard' ? 7 : $option['days'])) {
                                            $deliveryDate->addDay();
                                            if (! $deliveryDate->isWeekend()) $addedDays++;
                                        }
                                    @endphp

                                    <label class="flex items-start gap-3 rounded-xl border bg-white/70 p-4 cursor-pointer hover:bg-white transition" style="border-color: var(--theme-badge-ring);">
                                        <input type="radio"
                                               name="rush_option"
                                               value="{{ $option['days'] }}"
                                               data-fee="{{ $option['fee'] }}"
                                               data-label="{{ $option['label'] }}"
                                               class="rush-option-radio mt-1 theme-accent-text"
                                               style="accent-color: var(--theme-accent);"
                                               @if($option['days'] === 'standard') checked @endif>
                                        <div class="text-sm">
                                            <div class="font-semibold text-slate-800">
                                                {{ $option['label'] }}
                                                <span class="ml-1 theme-accent-text">
                                                    @if($option['fee'] > 0)
                                                        + {{ $currency }}{{ number_format($option['fee'], 2) }}
                                                    @else
                                                        (FREE)
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="mt-1 text-xs text-slate-500">Delivery by: {{ $deliveryDate->format('M d, Y') }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form id="payment-form" action="{{ route('invoices.pay', $invoice->id) }}" method="POST" class="mt-6">
                        @csrf
                        <input type="hidden" name="rush_enabled_value" id="rush_enabled_value" value="0">
                        <input type="hidden" name="rush_delivery_days" id="rush_delivery_days" value="standard">
                        <input type="hidden" name="rush_label" id="rush_label" value="">
                        <input type="hidden" name="rush_fee" id="rush_fee" value="0">

                        <button type="submit" class="w-full rounded-2xl px-6 py-4 font-semibold shadow-lg transition hover:brightness-110" style="background: var(--theme-header); color: var(--theme-button-text);">
                            <div class="flex items-center justify-center gap-2">
                                <i class="fas fa-lock"></i>
                                <span>Proceed to Secure Payment</span>
                                <span class="ml-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold" id="pay-pill">{{ $currency }}{{ number_format($grandTotal, 2) }}</span>
                            </div>
                        </button>
                    </form>

                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('invoices.download', $invoice->id) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <i class="fas fa-download"></i>
                            Download PDF
                        </a>
                        <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <i class="fas fa-print"></i>
                            Print
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-500">
                Questions about this invoice? Reply to the email or contact support — we usually respond within an hour.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyBtn = document.getElementById('copyUrlBtn');
            if (copyBtn) {
                copyBtn.addEventListener('click', function () {
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
            }

            const rushRadios = document.querySelectorAll('.rush-option-radio');
            const totalEl = document.getElementById('total-td');
            const headerAmount = document.getElementById('header-amount-due');
            const payPill = document.getElementById('pay-pill');

            const subtotal = parseFloat(@json($subtotal));
            const discount = parseFloat(@json($discount));
            const currency = @json($currency);
            const taxRate = @json($globalSettings->enable_tax ? (($globalSettings->tax_percentage ?? 0) / 100) : 0);

            function updateTotals(fee, days, label) {
                const tax = (subtotal + fee) * taxRate;
                const total = subtotal + fee + tax - discount;
                const formatted = currency + total.toFixed(2);
                if (totalEl) totalEl.textContent = formatted;
                if (headerAmount) headerAmount.textContent = formatted;
                if (payPill) payPill.textContent = formatted;

                const rushEnabled = document.getElementById('rush_enabled_value');
                const rushDays = document.getElementById('rush_delivery_days');
                const rushLabel = document.getElementById('rush_label');
                const rushFee = document.getElementById('rush_fee');
                if (rushEnabled) rushEnabled.value = fee > 0 ? 1 : 0;
                if (rushDays) rushDays.value = days;
                if (rushLabel) rushLabel.value = label;
                if (rushFee) rushFee.value = fee.toFixed(2);
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
