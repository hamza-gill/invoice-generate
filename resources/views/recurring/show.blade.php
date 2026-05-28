@extends('layouts.auth.app')

@section('title', $recurring->title . ' - ' . ($globalSettings->company_name ?? config('app.name')))
@php($hideNavbar = true)

@section('content')
    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('recurring.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $recurring->title }}</h2>
                    <p class="text-sm text-gray-500">Recurring Invoice Details</p>
                </div>
            </div>
            <div class="flex space-x-3">
                @if($recurring->status === 'active')
                    <form action="{{ route('recurring.pause', $recurring->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                            <i class="fas fa-pause mr-2"></i>Pause
                        </button>
                    </form>
                @elseif($recurring->status === 'paused')
                    <form action="{{ route('recurring.resume', $recurring->id) }}" method="POST" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-play mr-2"></i>Resume
                        </button>
                    </form>
                @endif

                <form action="{{ route('recurring.clone', $recurring->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-copy mr-2"></i>Clone
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="max-w-5xl mx-auto space-y-8">

            {{-- Status & Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                    <p class="text-sm text-gray-500 mb-2">Status</p>
                    <span class="px-4 py-2 text-sm font-semibold rounded-full
                        @if($recurring->status === 'active') text-green-600 bg-green-50
                        @elseif($recurring->status === 'paused') text-yellow-600 bg-yellow-50
                        @elseif($recurring->status === 'completed') text-blue-600 bg-blue-50
                        @elseif($recurring->status === 'cancelled') text-red-600 bg-red-50
                        @else text-gray-600 bg-gray-100 @endif">
                        {{ ucfirst($recurring->status) }}
                    </span>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                    <p class="text-sm text-gray-500 mb-2">Frequency</p>
                    <p class="text-xl font-bold text-gray-800">{{ ucfirst($recurring->frequency) }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                    <p class="text-sm text-gray-500 mb-2">Total Sent</p>
                    <p class="text-xl font-bold text-blue-600">{{ $recurring->total_sent ?? 0 }}</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center">
                    <p class="text-sm text-gray-500 mb-2">Next Send</p>
                    <p class="text-xl font-bold text-gray-800">
                        @if($recurring->next_send_date && $recurring->status === 'active')
                            {{ \Carbon\Carbon::parse($recurring->next_send_date)->format('M d, Y') }}
                        @else
                            <span class="text-gray-400 text-base">—</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Details --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Customer</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $recurring->customer->full_name ?? 'N/A' }}</p>
                        <p class="text-gray-500 text-sm">{{ $recurring->customer->email ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Amount</p>
                        <p class="font-semibold text-gray-800 mt-1 text-xl">{{ $globalSettings->base_currency ?? '$' }}{{ number_format($recurring->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Start Date</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ \Carbon\Carbon::parse($recurring->start_date)->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">End Date</p>
                        <p class="font-semibold text-gray-800 mt-1">
                            {{ $recurring->end_date ? \Carbon\Carbon::parse($recurring->end_date)->format('M d, Y') : 'No end date' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Max Occurrences</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $recurring->max_occurrences ?? 'Unlimited' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Auto-Send Email</p>
                        <p class="font-semibold mt-1">
                            @if($recurring->auto_send)
                                <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Yes</span>
                            @else
                                <span class="text-gray-400"><i class="fas fa-times-circle mr-1"></i>No</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($recurring->project_address)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <p class="text-sm text-gray-500">Project Address</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ $recurring->project_address }}</p>
                    </div>
                @endif

                @if($recurring->notes)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <p class="text-sm text-gray-500">Notes</p>
                        <p class="text-gray-700 mt-1 whitespace-pre-line">{{ $recurring->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Line Items --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    <i class="fas fa-list text-blue-600 mr-2"></i>Line Items
                </h3>
                <table class="w-full">
                    <thead class="border-b-2 border-gray-200">
                        <tr class="text-left">
                            <th class="pb-3 text-sm font-semibold text-gray-600">Service</th>
                            <th class="pb-3 text-sm font-semibold text-gray-600 text-center">Qty</th>
                            <th class="pb-3 text-sm font-semibold text-gray-600 text-right">Unit Price</th>
                            <th class="pb-3 text-sm font-semibold text-gray-600 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recurring->items as $item)
                            <tr>
                                <td class="py-4 text-gray-800">{{ $item->description ?? ($item->product->name ?? 'N/A') }}</td>
                                <td class="py-4 text-center text-gray-600">{{ $item->quantity }}</td>
                                <td class="py-4 text-right text-gray-600">{{ $globalSettings->base_currency ?? '$' }}{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-4 text-right font-semibold text-gray-800">{{ $globalSettings->base_currency ?? '$' }}{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @php
                    $subtotal = $recurring->items->sum(fn($i) => $i->quantity * $i->unit_price);
                    $discount = $recurring->discount ?? 0;
                    $total = max(0, $subtotal - $discount);
                @endphp

                <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                    <div class="w-72">
                        <div class="flex justify-between py-2 text-gray-600">
                            <span>Subtotal</span>
                            <span>{{ $globalSettings->base_currency ?? '$' }}{{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between py-2 text-red-500">
                                <span>Discount</span>
                                <span>-{{ $globalSettings->base_currency ?? '$' }}{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-3 border-t-2 border-gray-800 text-xl font-bold">
                            <span>Total</span>
                            <span>{{ $globalSettings->base_currency ?? '$' }}{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Generated Invoices --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    <i class="fas fa-file-invoice text-blue-600 mr-2"></i>Generated Invoices
                </h3>

                @if($recurring->invoices && $recurring->invoices->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
                                    <th class="p-3 rounded-tl-lg">Invoice #</th>
                                    <th class="p-3">Amount</th>
                                    <th class="p-3">Issue Date</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3 rounded-tr-lg text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recurring->invoices as $invoice)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="p-3">
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline font-semibold">
                                                #{{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td class="p-3 font-semibold">{{ $globalSettings->base_currency ?? '$' }}{{ number_format($invoice->amount, 2) }}</td>
                                        <td class="p-3 text-gray-600">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</td>
                                        <td class="p-3">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                @if($invoice->status === 'paid') text-green-600 bg-green-50
                                                @elseif($invoice->status === 'overdue') text-red-600 bg-red-50
                                                @elseif($invoice->status === 'draft') text-gray-600 bg-gray-100
                                                @else text-blue-600 bg-blue-50 @endif">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td class="p-3 text-right">
                                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline text-sm">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No invoices have been generated yet.</p>
                        <p class="text-gray-400 text-sm mt-1">Invoices will appear here as they are created based on the schedule.</p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Success', text: '{{ session("success") }}', timer: 2500, showConfirmButton: false });
            });
        </script>
    @endif
@endsection
