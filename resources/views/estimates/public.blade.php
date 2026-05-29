@extends('layouts.guest.app')
@section('title', 'Estimate #' . $estimate->estimate_number . ' - ' . ($globalSettings->company_name ?? config('app.name')))
@section('body-class', 'template-themed')
@section('theme-styles')
    @include('invoices.partials.template-theme-styles', ['templateTheme' => $templateTheme ?? []])
@endsection

@section('content')
    @php
        $currency = $globalSettings->base_currency ?? '$';
        $subtotal = $estimate->items->sum(fn ($item) => (float) $item->quantity * (float) $item->amount);
        $discount = (float) ($estimate->discount ?? 0);
        $grandTotal = max(0, $subtotal - $discount);
        $validLabel = $estimate->valid_until ? \Carbon\Carbon::parse($estimate->valid_until)->format('M d, Y') : null;
        $canRespond = $estimate->canBeApproved();
        $publicKey = $publicKey ?? $estimate->id;
    @endphp

    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="flex justify-center mb-4">
                <span class="inline-flex items-center gap-2 rounded-full border bg-white/60 px-3 py-1 text-xs text-slate-600 shadow-sm backdrop-blur" style="border-color: var(--theme-card-border);">
                    <i class="fas fa-file-signature theme-icon"></i>
                    Secure estimate · {{ $globalSettings->company_name ?? config('app.name') }}
                    @if(!empty($templateTheme['name']))
                        <span class="text-slate-400">·</span>
                        <span class="font-medium theme-accent-text">{{ $templateTheme['name'] }}</span>
                    @endif
                </span>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-600"></i>{{ session('error') }}
                </div>
            @endif

            <div class="rounded-3xl border bg-white/70 backdrop-blur overflow-hidden" style="border-color: var(--theme-card-border); box-shadow: var(--theme-card-shadow);">
                <div class="relative px-6 sm:px-8 py-7" style="background: var(--theme-header); color: var(--theme-header-text);">
                    <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35), transparent 40%), radial-gradient(circle at 85% 0%, rgba(255,255,255,0.25), transparent 45%);"></div>

                    <div class="relative flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <div class="text-xs font-semibold tracking-widest uppercase theme-header-text-muted">Estimate {{ $estimate->estimate_number }}</div>
                            <h1 class="mt-1 text-2xl sm:text-3xl font-bold theme-header-text">Review Estimate</h1>
                            <p class="mt-1 text-sm theme-header-text-muted">Please review the details below. Approve or decline when you're ready.</p>
                        </div>
                        <span class="inline-flex self-start items-center rounded-full px-4 py-2 text-sm font-semibold ring-1" style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.2);">
                            {{ ucfirst($estimate->status) }}
                        </span>
                    </div>

                    <div class="relative mt-6 rounded-2xl p-5 sm:p-6 ring-1" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.18);">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="text-[11px] font-semibold tracking-widest uppercase theme-header-text-muted">Estimated Total</div>
                                <div class="mt-1 text-3xl sm:text-4xl font-extrabold theme-header-text">{{ $currency }}{{ number_format($grandTotal, 2) }}</div>
                            </div>
                            @if($validLabel)
                                <div class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold ring-1" style="background: var(--theme-badge-bg); color: var(--theme-badge-text); border-color: var(--theme-badge-ring);">
                                    <i class="fas fa-calendar"></i>
                                    Valid until {{ $validLabel }}
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
                            'invoiceNumber' => $estimate->estimate_number,
                        ])
                    @else
                        <div class="overflow-x-auto rounded-2xl border border-slate-100">
                            <table class="min-w-full">
                                <thead class="bg-slate-50">
                                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-500">
                                        <th class="px-5 py-4 text-left">Description</th>
                                        <th class="px-5 py-4 text-center">Qty</th>
                                        <th class="px-5 py-4 text-right">Unit Price</th>
                                        <th class="px-5 py-4 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($estimate->items as $item)
                                        <tr>
                                            <td class="px-5 py-4 text-sm text-slate-800">{{ $item->description ?? $item->activity ?? ($item->product->name ?? 'Item') }}</td>
                                            <td class="px-5 py-4 text-sm text-center text-slate-600">{{ $item->quantity }}</td>
                                            <td class="px-5 py-4 text-sm text-right text-slate-600">{{ $currency }}{{ number_format($item->amount, 2) }}</td>
                                            <td class="px-5 py-4 text-sm text-right font-semibold text-slate-800">{{ $currency }}{{ number_format($item->quantity * $item->amount, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="px-5 py-5 text-right font-semibold text-slate-700">Total</td>
                                        <td class="px-5 py-5 text-right font-extrabold theme-accent-text">{{ $currency }}{{ number_format($grandTotal, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($canRespond)
                        <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-3">
                            <form action="{{ route('estimates.approve', $publicKey) }}" method="POST" class="w-full sm:flex-1">
                                @csrf
                                <button type="submit" class="w-full rounded-2xl px-6 py-4 font-semibold text-white shadow-lg transition hover:brightness-110 bg-emerald-600 hover:bg-emerald-700">
                                    <i class="fas fa-check-circle mr-2"></i>Approve Estimate
                                </button>
                            </form>
                            <form action="{{ route('estimates.decline', $publicKey) }}" method="POST" class="w-full sm:flex-1" onsubmit="return confirm('Decline this estimate?');">
                                @csrf
                                <button type="submit" class="w-full rounded-2xl px-6 py-4 font-semibold border-2 border-red-300 text-red-600 bg-white hover:bg-red-50 transition">
                                    <i class="fas fa-times-circle mr-2"></i>Decline
                                </button>
                            </form>
                        </div>
                        <p class="mt-4 text-center text-xs text-slate-500">By approving, you agree to the terms outlined in this estimate.</p>
                    @elseif($estimate->status === 'approved')
                        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-center">
                            <i class="fas fa-check-circle text-emerald-600 text-3xl mb-2"></i>
                            <p class="font-semibold text-emerald-800 text-lg">This estimate has been approved</p>
                            <p class="text-emerald-600 text-sm mt-1">Thank you for your approval.</p>
                        </div>
                    @elseif($estimate->status === 'declined')
                        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-6 text-center">
                            <i class="fas fa-times-circle text-red-600 text-3xl mb-2"></i>
                            <p class="font-semibold text-red-800 text-lg">This estimate has been declined</p>
                        </div>
                    @elseif($estimate->isExpired())
                        <div class="mt-6 rounded-2xl border border-orange-200 bg-orange-50 p-6 text-center">
                            <i class="fas fa-clock text-orange-600 text-3xl mb-2"></i>
                            <p class="font-semibold text-orange-800 text-lg">This estimate has expired</p>
                        </div>
                    @elseif($estimate->status === 'converted')
                        <div class="mt-6 rounded-2xl border border-indigo-200 bg-indigo-50 p-6 text-center">
                            <i class="fas fa-file-invoice-dollar text-indigo-600 text-3xl mb-2"></i>
                            <p class="font-semibold text-indigo-800 text-lg">Converted to an invoice</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-500">
                Questions? Contact {{ $globalSettings->contact_email ?? 'support' }} — we usually respond within an hour.
            </div>
        </div>
    </div>
@endsection
