@extends('layouts.auth.app')

@section('title', 'Customer Details -' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="mb-6">
        <a href="{{ route('customers.index') }}" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-arrow-left"></i> Back to Customers
        </a>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <!-- Customer Info -->
        <div class="col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Customer Details</h2>
            <p><strong>Name:</strong> {{ $customer->full_name  ?? 'N/A' }}</p>
            <p><strong>Email:</strong> {{ $customer->email ?? 'N/A' }}</p>
            <p><strong>Company:</strong> {{ $customer->company_name ?? 'N/A' }}</p>
            <p><strong>Street:</strong> {{ $customer->street_address ?? 'N/A' }}</p>
            <p><strong>City:</strong> {{ $customer->city ?? 'N/A' }}</p>
            <p><strong>State:</strong> {{ $customer->state ?? 'N/A' }}</p>
            <p><strong>Country:</strong> {{ $customer->country ?? 'N/A' }}</p>
            <p><strong>Zip:</strong> {{ $customer->zip ?? 'N/A' }}</p>
            <p><strong>Phone:</strong> {{ $customer->phone ?? 'N/A' }}</p>
            <p><strong>Created:</strong>
                {{ $customer->created_at ? $customer->created_at->diffForHumans() : 'N/A' }}
            </p>
        </div>

        <!-- Invoices Dashboard -->
        <div class="col-span-2 space-y-6">
            <!-- Summary Stats -->
            @php
                $totalInvoices = $customer->invoices->count();
                $paidInvoices = $customer->invoices->where('status', 'paid')->count();
                $pendingInvoices = $customer->invoices->where('status', 'pending')->count();
                $sentInvoices = $customer->invoices->where('status', 'sent')->count();
                $totalPaid = $customer->invoices->where('status', 'paid')->sum('total');
            @endphp

            <div class="grid grid-cols-5 gap-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-sm text-gray-500">Total Invoices</h3>
                    <p class="text-2xl font-semibold text-gray-800">{{ $totalInvoices }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-sm text-gray-500">Paid</h3>
                    <p class="text-2xl font-semibold text-green-600">{{ $paidInvoices }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-sm text-gray-500">Pending</h3>
                    <p class="text-2xl font-semibold text-yellow-500">{{ $pendingInvoices }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-sm text-gray-500">Sent</h3>
                    <p class="text-2xl font-semibold text-blue-500">{{ $sentInvoices }}</p>
                </div>
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-sm text-gray-500">Total Paid</h3>
                    <p class="text-2xl font-semibold text-indigo-600">${{ number_format($totalPaid, 2) }}</p>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-semibold mb-4 text-gray-800">Invoice Status Overview</h2>

                @php
                    $totalForChart = $paidInvoices + $pendingInvoices + $sentInvoices;
                @endphp

                @if($totalForChart === 0)
                    <div class="py-8 text-center text-gray-600">No invoice data to display.</div>
                @else
                    <div class="w-full" style="height: 260px;">
                        <canvas id="invoiceChart" style="width:100%;height:100%;display:block;"></canvas>
                    </div>
                @endif
            </div>

            <!-- Invoices Table -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Invoices</h2>
                    <a href="{{ route('invoices.create', ['customer_id' => $customer->id]) }}"
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> New Invoice
                    </a>
                </div>

                @if($customer->invoices->count() > 0)
                    <table class="min-w-full border-collapse">
                        <thead class="bg-gray-50 text-gray-600 text-sm uppercase">
                        <tr>
                            <th class="p-3 border-b">Invoice #</th>
                            <th class="p-3 border-b">Amount</th>
                            <th class="p-3 border-b">Status</th>
                            <th class="p-3 border-b">Date</th>
                            <th class="p-3 border-b text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($customer->invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="p-3 border-b">{{ $invoice->invoice_number }}</td>
                                <td class="p-3 border-b">${{ number_format($invoice->total, 2) }}</td>
                                <td class="p-3 border-b">
                                <span class="px-3 py-1 text-xs rounded-full
                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' :
                                       ($invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                                       ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700')) }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                                </td>
                                <td class="p-3 border-b">
                                    {{ $invoice->created_at ? $invoice->created_at->diffForHumans() : 'N/A' }}
                                </td>
                                <td class="p-3 border-b text-right">
                                    <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-600 text-center py-8">No invoices yet.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paidCount = @json($paidInvoices);
            const pendingCount = @json($pendingInvoices);
            const sentCount = @json($sentInvoices);
            const total = paidCount + pendingCount + sentCount;

            if (total === 0) return;

            const ctx = document.getElementById('invoiceChart').getContext('2d');
            if (window.__invoiceChartInstance) {
                try { window.__invoiceChartInstance.destroy(); } catch (e) {}
            }

            window.__invoiceChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Pending', 'Sent'],
                    datasets: [{
                        data: [paidCount, pendingCount, sentCount],
                        backgroundColor: ['#22c55e', '#facc15', '#3b82f6'],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: '#374151' }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.raw ?? 0;
                                    const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
