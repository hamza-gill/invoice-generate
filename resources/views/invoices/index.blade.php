@extends('layouts.auth.app')

@section('title', 'Invoices - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Invoices</h1>

        <div class="space-x-2">
            <a href="{{ route('invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i> Create Invoice
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input type="text" id="searchInvoice"
               placeholder="Search by invoice number, client, or status..."
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
    </div>

    <!-- Invoice Table -->
    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
            <tr class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
                <th class="p-4 border-b">Invoice #</th>
                <th class="p-4 border-b">Client</th>
                <th class="p-4 border-b">Amount</th>
                <th class="p-4 border-b">Issue Date</th>
                <th class="p-4 border-b">Due Date</th>
                <th class="p-4 border-b">Status</th>
                <th class="p-4 border-b text-right">Actions</th>
            </tr>
            </thead>
            <tbody id="invoiceTableBody">
            @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 border-b font-semibold">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                            #{{ $invoice->invoice_number }}
                        </a>
                    </td>
                    <td class="p-4 border-b">{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td class="p-4 border-b font-semibold">USD {{ number_format($invoice->amount, 2) }}</td>
                    <td class="p-4 border-b text-gray-600">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</td>
                    <td class="p-4 border-b text-gray-600">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</td>
                    <td class="p-4 border-b">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($invoice->status == 'paid') text-green-600 bg-green-50
                            @elseif($invoice->status == 'overdue') text-red-600 bg-red-50
                            @elseif($invoice->status == 'draft') text-gray-600 bg-gray-100
                            @else text-blue-600 bg-blue-50 @endif">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="p-4 border-b text-right space-x-3">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('invoices.edit', $invoice->id) }}" class="text-gray-600 hover:underline">Edit</a>
                        <a href="{{ route('invoices.download', $invoice->id) }}" class="text-gray-600 hover:underline">Download</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">No invoices found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $invoices->links('components.pagination') }}
    </div>


    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInvoice');
            const tableBody = document.getElementById('invoiceTableBody');
            let timeout = null;

            searchInput.addEventListener('keyup', function () {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const query = this.value.trim();

                    tableBody.innerHTML = `
                        <tr><td colspan="7" class="text-center py-6 text-gray-500">Searching...</td></tr>
                    `;

                    fetch(`{{ route('invoices.search') }}?query=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            const invoices = data.data;
                            if (!invoices || invoices.length === 0) {
                                tableBody.innerHTML = `
                                    <tr><td colspan="7" class="text-center py-6 text-gray-500">No matching invoices found.</td></tr>
                                `;
                                return;
                            }

                            tableBody.innerHTML = invoices.map(inv => `
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-4 border-b font-semibold">
                                        <a href="/invoices/${inv.id}" class="text-blue-600 hover:underline">INV-${inv.invoice_number}</a>
                                    </td>
                                    <td class="p-4 border-b">${inv.customer_name ?? 'N/A'}</td>
                                    <td class="p-4 border-b font-semibold">USD ${parseFloat(inv.amount).toFixed(2)}</td>
                                    <td class="p-4 border-b">${inv.issue_date_formatted ?? 'N/A'}</td>
                                    <td class="p-4 border-b">${inv.due_date_formatted ?? 'N/A'}</td>
                                    <td class="p-4 border-b">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full ${
                                inv.status === 'paid' ? 'text-green-600 bg-green-50' :
                                    inv.status === 'overdue' ? 'text-red-600 bg-red-50' :
                                        inv.status === 'draft' ? 'text-gray-600 bg-gray-100' :
                                            'text-blue-600 bg-blue-50'
                            }">${inv.status.charAt(0).toUpperCase() + inv.status.slice(1)}</span>
                                    </td>
                                    <td class="p-4 border-b text-right space-x-3">
                                        <a href="/invoices/${inv.id}" class="text-blue-600 hover:underline">View</a>
                                        <a href="/invoices/${inv.id}/edit" class="text-gray-600 hover:underline">Edit</a>
                                        <a href="/invoices/${inv.id}/download" class="text-gray-600 hover:underline">Download</a>
                                    </td>
                                </tr>
                            `).join('');
                        })
                        .catch(() => {
                            tableBody.innerHTML = `
                                <tr><td colspan="7" class="text-center py-6 text-red-500">Error loading data.</td></tr>
                            `;
                        });
                }, 300);
            });
        });
    </script>
@endsection
