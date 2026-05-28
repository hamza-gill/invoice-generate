@extends('layouts.auth.app')

@section('title', 'Estimate #' . $estimate->estimate_number . ' - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    @php
        // Ensure timeline variables are always defined (even if view cache gets stale).
        $timelineStatuses = $statuses ?? ['draft', 'sent', 'viewed', 'approved', 'converted'];
        if (!is_array($timelineStatuses)) {
            $timelineStatuses = collect($timelineStatuses)->values()->all();
        }

        $isDeclined = $isDeclined ?? ($estimate->status === 'declined');

        $currentIndex = -1;
        $foundIndex = array_search($estimate->status, $timelineStatuses, true);
        if ($foundIndex !== false) {
            $currentIndex = (int) $foundIndex;
        }
    @endphp

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('estimates.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Estimate #{{ $estimate->estimate_number }}</h2>
                    <p class="text-sm text-gray-500">Estimate Details</p>
                </div>
            </div>
            <div class="flex space-x-3">
                @if(in_array($estimate->status, ['draft']))
                    <a href="{{ route('estimates.edit', $estimate->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif

                @if(in_array($estimate->status, ['draft', 'sent', 'viewed']))
                    <button id="sendEmailBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Send to Client
                    </button>
                @endif

                @if($estimate->status === 'approved')
                    <form action="{{ route('estimates.convert', $estimate->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>Convert to Invoice
                        </button>
                    </form>
                @endif

                <button id="copyLinkBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-link mr-2"></i>Copy Public Link
                </button>

                <form action="{{ route('estimates.destroy', $estimate->id) }}" method="POST" class="inline" id="deleteForm">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="max-w-4xl mx-auto space-y-8">

            {{-- Status Timeline --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-4">Status Timeline</h3>
                <div class="flex items-center justify-between">
                    @php
                        $timelineStatuses = $statuses ?? ['draft', 'sent', 'viewed', 'approved', 'converted'];
                        if (!is_array($timelineStatuses)) {
                            $timelineStatuses = collect($timelineStatuses)->values()->all();
                        }

                        $isDeclined = ($isDeclined ?? ($estimate->status === 'declined'));

                        $currentIndex = -1;
                        $foundIndex = array_search($estimate->status, $timelineStatuses, true);
                        if ($foundIndex !== false) {
                            $currentIndex = (int) $foundIndex;
                        }
                    @endphp
                    @foreach($timelineStatuses as $i => $status)
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                                @if(($isDeclined ?? false) && $status === 'approved') bg-red-100 text-red-600
                                @elseif($i <= $currentIndex) bg-blue-600 text-white
                                @else bg-gray-200 text-gray-400 @endif">
                                @if(($isDeclined ?? false) && $status === 'approved')
                                    <i class="fas fa-times"></i>
                                @elseif($i <= $currentIndex)
                                    <i class="fas fa-check"></i>
                                @else
                                    {{ $i + 1 }}
                                @endif
                            </div>
                            <p class="text-xs mt-2 font-medium
                                @if(($isDeclined ?? false) && $status === 'approved') text-red-600
                                @elseif($i <= $currentIndex) text-blue-600
                                @else text-gray-400 @endif">
                                {{ (($isDeclined ?? false) && $status === 'approved') ? 'Declined' : ucfirst($status) }}
                            </p>
                        </div>
                        @if(!$loop->last)
                            <div class="flex-1 h-0.5 mt-[-1rem] @if($i < $currentIndex) bg-blue-600 @else bg-gray-200 @endif"></div>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Estimate Preview --}}
            <div class="bg-white p-12 rounded-2xl shadow-sm border border-gray-100" id="estimateContent">

                {{-- Header --}}
                <div class="flex justify-between mb-12">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-800 mb-2">ESTIMATE</h1>
                        <p class="text-lg text-gray-600">#{{ $estimate->estimate_number }}</p>
                        @if(!empty($estimate->project_address))
                            <p class="text-sm font-bold text-gray-500 mt-4 mb-1">PROJECT ADDRESS:</p>
                            <p class="text-gray-800 whitespace-pre-line">{{ $estimate->project_address }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        @if(!empty($globalSettings->logo_path))
                            <img src="{{ asset('storage/' . $globalSettings->logo_path) }}" alt="Logo" class="h-12 mb-2 ml-auto">
                        @endif
                        <h2 class="text-xl font-bold text-gray-800">{{ $globalSettings->company_name ?? config('app.name') }}</h2>
                        <p class="text-gray-600">{{ $globalSettings->address ?? '' }}</p>
                        @if($globalSettings->contact_email)
                            <p class="text-gray-600">
                                <a href="mailto:{{ $globalSettings->contact_email }}" class="text-blue-600 hover:underline">{{ $globalSettings->contact_email }}</a>
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Billing Info --}}
                <div class="grid grid-cols-2 gap-8 mb-12">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">PREPARED FOR:</p>
                        <p class="font-semibold text-gray-800">{{ $estimate->customer->full_name ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $estimate->customer->email ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $estimate->customer->address ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <span class="text-gray-500">Issue Date:</span>
                            <span class="font-semibold">{{ \Carbon\Carbon::parse($estimate->issue_date)->format('M d, Y') }}</span>

                            @if($estimate->valid_until)
                                <span class="text-gray-500">Valid Until:</span>
                                <span class="font-semibold">{{ \Carbon\Carbon::parse($estimate->valid_until)->format('M d, Y') }}</span>
                            @endif

                            <span class="text-gray-500">Status:</span>
                            <span>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($estimate->status === 'draft') text-gray-600 bg-gray-100
                                    @elseif($estimate->status === 'sent') text-blue-600 bg-blue-50
                                    @elseif($estimate->status === 'viewed') text-purple-600 bg-purple-50
                                    @elseif($estimate->status === 'approved') text-green-600 bg-green-50
                                    @elseif($estimate->status === 'declined') text-red-600 bg-red-50
                                    @elseif($estimate->status === 'converted') text-indigo-600 bg-indigo-50
                                    @elseif($estimate->status === 'expired') text-orange-600 bg-orange-50
                                    @else text-gray-600 bg-gray-100 @endif">
                                    {{ strtoupper($estimate->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Line Items --}}
                @php
                    $currency = $globalSettings->base_currency ?? '$';
                    $subtotal = $estimate->items->sum(fn($item) => $item->quantity * $item->unit_price);
                    $discount = $estimate->discount ?? 0;
                    $total = max(0, $subtotal - $discount);
                @endphp

                <table class="w-full mb-8">
                    <thead class="border-b-2 border-gray-300">
                        <tr class="text-left">
                            <th class="pb-4 text-sm font-semibold text-gray-600">SERVICE</th>
                            <th class="pb-4 text-sm font-semibold text-gray-600 text-center">QTY</th>
                            <th class="pb-4 text-sm font-semibold text-gray-600 text-right">PRICE</th>
                            <th class="pb-4 text-sm font-semibold text-gray-600 text-right">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($estimate->items as $item)
                            <tr>
                                <td class="py-4">{{ $item->description ?? ($item->product->name ?? 'N/A') }}</td>
                                <td class="py-4 text-center">{{ $item->quantity }}</td>
                                <td class="py-4 text-right">{{ $currency }}{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-4 text-right font-semibold">{{ $currency }}{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="flex justify-end mb-12">
                    <div class="w-64">
                        <div class="flex justify-between py-2 border-t border-gray-200">
                            <span class="font-semibold">Subtotal:</span>
                            <span>{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between py-2 border-t border-gray-200 text-red-600 font-semibold">
                                <span>Discount:</span>
                                <span>-{{ $currency }}{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-3 border-t-2 border-gray-800 text-xl font-bold">
                            <span>Total:</span>
                            <span>{{ $currency }}{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Custom Fields --}}
                @if($estimate->custom_fields && count($estimate->custom_fields) > 0)
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="font-bold text-gray-800 mb-3">Additional Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($estimate->custom_fields as $field)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 uppercase">{{ $field['key'] }}</p>
                                    <p class="text-gray-800 font-medium mt-1">{{ $field['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notes --}}
                @if(!empty($estimate->notes))
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="font-bold text-gray-800 mb-2">Notes</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $estimate->notes }}</p>
                    </div>
                @endif

                {{-- Converted Invoice Link --}}
                @if($estimate->status === 'converted' && $estimate->invoice_id)
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-file-invoice-dollar text-indigo-600 text-xl"></i>
                                <div>
                                    <p class="font-semibold text-indigo-800">Converted to Invoice</p>
                                    <p class="text-sm text-indigo-600">This estimate has been converted to an invoice.</p>
                                </div>
                            </div>
                            <a href="{{ route('invoices.show', $estimate->invoice_id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold transition">
                                View Invoice <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('copyLinkBtn')?.addEventListener('click', function() {
            const publicUrl = "{{ route('estimates.public', $estimate->id) }}";
            navigator.clipboard.writeText(publicUrl).then(() => {
                Swal.fire({ icon: 'success', title: 'Link Copied!', text: 'Public URL copied to clipboard.', timer: 2000, showConfirmButton: false });
            }).catch(() => {
                Swal.fire({ icon: 'error', title: 'Copy Failed', text: 'Could not copy link.' });
            });
        });

        document.getElementById('sendEmailBtn')?.addEventListener('click', function() {
            Swal.fire({
                title: 'Send Estimate?',
                text: 'This will email the estimate to the client.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';

                fetch('{{ route("estimates.send", $estimate->id) }}', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    Swal.fire({ icon: data.success ? 'success' : 'error', title: data.success ? 'Sent!' : 'Error', text: data.message });
                })
                .catch(() => { Swal.fire('Error', 'An error occurred.', 'error'); })
                .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Send to Client'; });
            });
        });

        document.getElementById('deleteForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Delete Estimate?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) e.target.submit();
            });
        });
    </script>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Success', text: '{{ session("success") }}', timer: 2500, showConfirmButton: false });
            });
        </script>
    @endif
@endsection
