@extends('layouts.auth.app')

@section('title', 'Dashboard - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="w-full max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Overview</h1>

        {{-- ✅ Stats --}}
        @include('dashboard.stats')

        {{-- ✅ Charts --}}
        @include('dashboard.charts')

        {{-- ✅ Recent Invoices Section --}}
        <div class="mt-10 bg-white rounded-xl shadow border border-gray-100">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Invoices</h2>
                <a href="{{ route('invoices.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Invoice #</th>
                        <th class="px-6 py-3 text-left">Client</th>
                        <th class="px-6 py-3 text-left">Amount</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Issue Date</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($recentInvoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ $invoice->customer->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-semibold">USD {{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    @if($invoice->status === 'paid') bg-green-100 text-green-700
                                    @elseif($invoice->status === 'sent') bg-blue-100 text-blue-700
                                    @elseif($invoice->status === 'overdue') bg-red-100 text-red-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('invoices.show', $invoice->id) }}"
                                   class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">No recent invoices found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ✅ Recent Payments Section --}}
        <div class="mt-10 bg-white rounded-xl shadow border border-gray-100">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Customer Payments</h2>
                <a href="{{ route('invoices.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Invoice #</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">Amount</th>
                        <th class="px-6 py-3 text-left">Paid On</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($recentPaidInvoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">
                                    INV-{{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">{{ $invoice->customer->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-semibold">USD {{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($invoice->updated_at)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('invoices.show', $invoice->id) }}"
                                   class="text-blue-600 hover:underline">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">No recent payments found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ✅ Recent Customers --}}
        <div class="mt-10 bg-white rounded-xl shadow border border-gray-100">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Customers</h2>
                <a href="{{ route('customers.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Company</th>
                        <th class="px-6 py-3 text-left">Country</th>
                        <th class="px-6 py-3 text-right">Invoices</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @forelse($recentCustomers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $customer->name }}</td>
                            <td class="px-6 py-4">{{ $customer->email }}</td>
                            <td class="px-6 py-4">{{ $customer->company_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $customer->country ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right">{{ $customer->invoices->count() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500">No recent customers found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
