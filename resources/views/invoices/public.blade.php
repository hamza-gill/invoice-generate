@extends('layouts.guest.app')
@section('title', 'View Invoice - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    @php
        $currency = $globalSettings->base_currency ?? '$';

        $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
        $rushFee = ($invoice->rush_enabled_value ? ($invoice->rush_fee ?? 0) : 0);
        $discount = $invoice->discount ?? 0;

        $taxAmount = ($globalSettings->enable_tax ?? false)
            ? (($subtotal + $rushFee) * (($globalSettings->tax_percentage ?? 0) / 100))
            : 0;

        $grandTotal = max(0, $subtotal + $rushFee + $taxAmount - $discount);
        $dueLabel = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : null;

        $canPay = !in_array($invoice->payment_status, ['paid', 'completed'], true) && $invoice->status !== 'paid';
    @endphp

    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="flex justify-center mb-4">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/50 bg-white/60 px-3 py-1 text-xs text-slate-600 shadow-sm backdrop-blur">
                    <i class="fas fa-shield-alt text-indigo-600"></i>
                    Secure invoice · Encrypted end-to-end
                </span>
            </div>

            <div class="rounded-3xl border border-white/60 bg-white/70 shadow-[0_18px_60px_-20px_rgba(79,70,229,0.35)] backdrop-blur overflow-hidden">
                <div class="relative px-8 py-7" style="background: var(--bloom-header);">
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35), transparent 40%), radial-gradient(circle at 85% 0%, rgba(255,255,255,0.25), transparent 45%);"></div>

                    <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold tracking-widest uppercase text-white/80">Invoice {{ $invoice->invoice_number }}</div>
                            <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-white">Invoice Details</h1>
                            <p class="mt-1 text-sm text-white/80">Review the invoice details below. Use the button to pay securely when ready.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 justify-start sm:justify-end">
                            <button id="copyUrlBtn" class="inline-flex items-center gap-2 rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/25 hover:bg-white/20 transition">
                                <i class="fas fa-link"></i>
                                Copy Link
                            </button>

                            @if(!$canPay)
                                <a href="{{ route('invoices.download', $invoice->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500/20 px-4 py-2 text-sm font-semibold text-white ring-1 ring-emerald-300/25 hover:bg-emerald-500/25 transition">
                                    <i class="fas fa-download"></i>
                                    Download PDF
                                </a>
                            @else
                                <a href="{{ route('invoices.accept.page', $invoice->id) }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-bold text-indigo-700 shadow-sm hover:bg-white/90 transition">
                                    <i class="fas fa-lock"></i>
                                    Pay Now
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="relative mt-6 rounded-2xl bg-white/10 ring-1 ring-white/20 p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="text-[11px] font-semibold tracking-widest uppercase text-white/80">Amount Due</div>
                                <div class="mt-1 text-3xl sm:text-4xl font-extrabold text-white">{{ $currency }}{{ number_format($grandTotal, 2) }}</div>
                            </div>
                            <div class="flex items-center gap-3 flex-wrap justify-start sm:justify-end">
                                @if($dueLabel)
                                    <div class="inline-flex items-center gap-2 rounded-full bg-emerald-500/15 px-4 py-2 text-sm font-semibold text-emerald-100 ring-1 ring-emerald-400/25">
                                        <i class="fas fa-calendar text-emerald-200"></i>
                                        Due {{ $dueLabel }}
                                    </div>
                                @endif
                                <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/20">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5">
                            <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-user-circle text-indigo-600"></i>
                                Customer Information
                            </div>
                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-4"><span class="text-slate-500">Name</span><span class="font-semibold text-slate-800">{{ $invoice->customer->full_name ?? 'N/A' }}</span></div>
                                <div class="flex items-center justify-between gap-4"><span class="text-slate-500">Email</span><span class="font-semibold text-slate-800">{{ $invoice->customer->email ?? 'N/A' }}</span></div>
                                <div class="flex items-center justify-between gap-4"><span class="text-slate-500">Address</span><span class="font-semibold text-slate-800">{{ $invoice->project_address ?? ($invoice->customer->address ?? 'N/A') }}</span></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5">
                            <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <i class="fas fa-file-invoice text-indigo-600"></i>
                                Invoice Details
                            </div>
                            <div class="mt-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between gap-4"><span class="text-slate-500">Invoice #</span><span class="font-semibold text-slate-800">{{ $invoice->invoice_number }}</span></div>
                                <div class="flex items-center justify-between gap-4"><span class="text-slate-500">Issue Date</span><span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</span></div>
                                <div class="flex items-center justify-between gap-4"><span class="text-slate-500">Due Date</span><span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-100">
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
                                    <td class="px-5 py-5 text-base text-right font-extrabold text-indigo-700">{{ $currency }}{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    </div>
                    @endif

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 px-2 sm:px-0">
                        <a href="{{ route('invoices.download', $invoice->id) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <i class="fas fa-download"></i>
                            Download PDF
                        </a>
                        @if($canPay)
                            <a href="{{ route('invoices.accept.page', $invoice->id) }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold text-white shadow-lg transition hover:brightness-110" style="background: var(--bloom-header);">
                                <i class="fas fa-lock"></i>
                                Pay Securely ({{ $currency }}{{ number_format($grandTotal, 2) }})
                            </a>
                        @else
                            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                <i class="fas fa-print"></i>
                                Print
                            </button>
                        @endif
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-[11px] text-slate-500">
                        <span class="inline-flex items-center gap-2"><i class="fas fa-shield-alt text-emerald-600"></i> 256-bit SSL secured</span>
                        <span class="inline-flex items-center gap-2"><i class="fas fa-credit-card text-indigo-600"></i> Visa, Mastercard, Amex</span>
                        <span class="inline-flex items-center gap-2"><i class="fas fa-check-circle text-emerald-600"></i> PCI-DSS compliant</span>
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
            if (!copyBtn) return;
            copyBtn.addEventListener('click', function () {
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
                    Swal.fire({ icon: 'error', title: 'Copy Failed', text: 'Unable to copy URL. Try again.' });
                });
            });
        });
    </script>
@endsection
