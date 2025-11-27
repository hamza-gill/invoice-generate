@extends('layouts.auth.app')

@section('title', $product->name . ' - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="w-full max-w-7xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $product->name }}</h1>
                <p class="text-gray-500 mt-1">View details, stats, and related invoices for this product.</p>
            </div>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Products
            </a>
        </div>

        {{-- Product Info Card --}}
        <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Product Information</h2>
                    <ul class="text-sm text-gray-700 space-y-2">
                        <li><strong>Name:</strong> {{ $product->name }}</li>
                        <li><strong>Category:</strong> {{ $product->category ?? 'Uncategorized' }}</li>
                        <li><strong>Price:</strong> ${{ number_format($product->price, 2) }}</li>
                        <li>
                            <strong>Status:</strong>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Description</h2>
                    <p class="text-gray-700 text-sm leading-relaxed">
                        {{ $product->description ?? 'No description provided for this product.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Stats Section --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow border border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm">Total Invoices</h3>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalInvoices }}</p>
                </div>
                <i class="fas fa-file-invoice text-blue-500 text-3xl"></i>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow border border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm">Paid Invoices</h3>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $paidInvoices }}</p>
                </div>
                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow border border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-gray-500 text-sm">Pending Invoices</h3>
                    <p class="text-2xl font-bold text-yellow-500 mt-1">{{ $pendingInvoices }}</p>
                </div>
                <i class="fas fa-clock text-yellow-500 text-3xl"></i>
            </div>
        </div>

        {{-- Related Invoices Table --}}
        <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Related Invoices</h2>
                <span class="text-sm text-gray-500">{{ $invoices->count() }} total</span>
            </div>

            <table class="min-w-full border-collapse">
                <thead class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
                <tr>
                    <th class="p-4 border-b">Invoice #</th>
                    <th class="p-4 border-b">Client</th>
                    <th class="p-4 border-b">Amount</th>
                    <th class="p-4 border-b">Issue Date</th>
                    <th class="p-4 border-b">Due Date</th>
                    <th class="p-4 border-b">Status</th>
                    <th class="p-4 border-b text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 border-b font-semibold">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="p-4 border-b">{{ $invoice->customer->name ?? 'N/A' }}</td>
                        <td class="p-4 border-b font-semibold text-gray-800">
                            ${{ number_format($invoice->amount, 2) }}
                        </td>
                        <td class="p-4 border-b text-gray-600">
                            {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}
                        </td>
                        <td class="p-4 border-b text-gray-600">
                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}
                        </td>
                        <td class="p-4 border-b">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($invoice->status === 'paid') bg-green-100 text-green-700
                                    @elseif($invoice->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                        </td>
                        <td class="p-4 border-b text-right">
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">No invoices found for this product.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
