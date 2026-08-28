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

        // ---- Banner color: adopt the invoice template's own primary color ----
        $templateConfig = $invoiceTemplate->config ?? [];
        $primary = $templateConfig['primary_color'] ?? '#4F46E5'; // indigo fallback if no template is set

        $hexToRgb = function (string $hex): array {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (strlen($hex) !== 6) {
                $hex = '4F46E5';
            }
            return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
        };

        $relativeLuminance = function (array $rgb): float {
            [$r, $g, $b] = array_map(function ($c) {
                $c /= 255;
                return $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
            }, $rgb);
            return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
        };

        $luminance = $relativeLuminance($hexToRgb($primary));
        $isLightBg = $luminance > 0.55;
        $headerTextColor = $isLightBg ? '#1f2937' : '#ffffff';
        $ghostBtnClass = $isLightBg
            ? 'bg-black/5 hover:bg-black/10 ring-1 ring-black/10'
            : 'bg-white/15 hover:bg-white/20 ring-1 ring-white/25';

        $statusStyles = match ($invoice->status) {
            'paid' => ['bg-emerald-50', 'text-emerald-700', 'ring-emerald-200'],
            'void' => ['bg-red-50', 'text-red-700', 'ring-red-200'],
            'sent' => ['bg-blue-50', 'text-blue-700', 'ring-blue-200'],
            default => ['bg-slate-100', 'text-slate-600', 'ring-slate-200'],
        };
    @endphp

    <div class="flex justify-center px-3 sm:px-4 py-4 sm:py-8">
        <div class="w-full max-w-4xl">

            <div class="flex justify-center mb-4">
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] sm:text-xs text-slate-500 shadow-sm text-center">
                    <i class="fas fa-shield-alt text-emerald-500"></i>
                    Secure invoice &middot; Encrypted end-to-end
                </span>
            </div>

            <div class="rounded-2xl sm:rounded-3xl border border-slate-200 bg-white shadow-xl overflow-hidden">

                {{-- Branded header — colored from the invoice's own template --}}
                <div class="px-4 sm:px-8 py-5 sm:py-6" style="background: {{ $primary }}; color: {{ $headerTextColor }};">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="min-w-0">
                            <div class="text-[10px] sm:text-[11px] font-semibold tracking-widest uppercase opacity-80">Invoice</div>
                            <h1 class="mt-0.5 text-xl sm:text-2xl md:text-3xl font-bold break-words">{{ $invoice->invoice_number }}</h1>
                            @if($globalSettings->company_name ?? false)
                                <p class="mt-1 text-sm opacity-85 break-words">{{ $globalSettings->company_name }}</p>
                            @endif
                        </div>

                        <div class="flex flex-col xs:flex-row sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                            <button id="copyUrlBtn"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 sm:py-2 text-sm font-semibold transition w-full sm:w-auto {{ $ghostBtnClass }}"
                                    style="color: {{ $headerTextColor }};">
                                <i class="fas fa-link"></i>
                                Copy Link
                            </button>

                            @if(!$canPay)
                                <a href="{{ route('invoices.download', $invoice->id) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 sm:py-2 text-sm font-bold shadow-sm hover:opacity-90 transition w-full sm:w-auto"
                                   style="color: {{ $primary }};">
                                    <i class="fas fa-download"></i>
                                    Download PDF
                                </a>
                            @else
                                <a href="{{ route('invoices.accept.page', $invoice->id) }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 sm:py-2 text-sm font-bold shadow-sm hover:opacity-90 transition w-full sm:w-auto"
                                   style="color: {{ $primary }};">
                                    <i class="fas fa-lock"></i>
                                    Pay Now
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Amount / status summary — deliberately neutral so it stays readable regardless of the template color above --}}
                <div class="px-4 sm:px-8 py-5 bg-slate-50 border-b border-slate-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="text-[10px] sm:text-[11px] font-semibold tracking-widest uppercase text-slate-400">Amount Due</div>
                            <div class="mt-1 text-2xl sm:text-3xl md:text-4xl font-extrabold break-words" style="color: {{ $primary }};">
                                {{ $currency }}{{ number_format($grandTotal, 2) }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap justify-start sm:justify-end">
                            @if($dueLabel)
                                <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 sm:px-4 py-2 text-xs sm:text-sm font-medium text-slate-600 ring-1 ring-slate-200">
                                    <i class="fas fa-calendar text-slate-400"></i>
                                    Due {{ $dueLabel }}
                                </span>
                            @endif
                            <span class="inline-flex items-center rounded-full px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold ring-1 {{ $statusStyles[0] }} {{ $statusStyles[1] }} {{ $statusStyles[2] }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Invoice document --}}
                <div class="p-2 sm:p-4 md:p-6 bg-white">
                    @if(!empty($invoiceDocumentSrcdoc))
                        @include('invoices.partials.template-document', [
                            'invoiceDocumentSrcdoc' => $invoiceDocumentSrcdoc,
                            'templateName' => $invoiceTemplate?->name,
                            'invoiceNumber' => $invoice->invoice_number,
                        ])
                    @else
                        <div class="p-1 sm:p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-5">
                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <i class="fas fa-user-circle" style="color: {{ $primary }};"></i>
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
                                        <i class="fas fa-file-invoice" style="color: {{ $primary }};"></i>
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
                                        <td class="px-5 py-5 text-base text-right font-extrabold" style="color: {{ $primary }};">{{ $currency }}{{ number_format($grandTotal, 2) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 px-1 sm:px-0">
                        <a href="{{ route('invoices.download', $invoice->id) }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            <i class="fas fa-download"></i>
                            Download PDF
                        </a>
                        @if($canPay)
                            <a href="{{ route('invoices.accept.page', $invoice->id) }}"
                               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-semibold shadow-lg transition hover:brightness-110 text-center"
                               style="background: {{ $primary }}; color: {{ $headerTextColor }};">
                                <i class="fas fa-lock"></i>
                                <span class="truncate">Pay Securely ({{ $currency }}{{ number_format($grandTotal, 2) }})</span>
                            </a>
                        @else
                            <button type="button" onclick="window.print()" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                                <i class="fas fa-print"></i>
                                Print
                            </button>
                        @endif
                    </div>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 sm:gap-6 text-[10px] sm:text-[11px] text-slate-500 px-2">
                        <span class="inline-flex items-center gap-1.5 sm:gap-2"><i class="fas fa-shield-alt text-emerald-600"></i> 256-bit SSL secured</span>
                        <span class="inline-flex items-center gap-1.5 sm:gap-2"><i class="fas fa-credit-card text-slate-400"></i> Visa, Mastercard, Amex</span>
                        <span class="inline-flex items-center gap-1.5 sm:gap-2"><i class="fas fa-check-circle text-emerald-600"></i> PCI-DSS compliant</span>
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
