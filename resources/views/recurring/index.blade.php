@extends('layouts.auth.app')

@section('title', 'Recurring Invoices - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Recurring Invoices</h1>
            <p class="text-gray-500 mt-1">Manage your automated recurring invoices</p>
        </div>
        <a href="{{ route('recurring.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Create Recurring Invoice
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-sync-alt text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Recurring</p>
                    <p class="text-xl font-bold text-gray-800">{{ $recurringInvoices->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-play-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Active</p>
                    <p class="text-xl font-bold text-gray-800">{{ $recurringInvoices->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-yellow-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-pause-circle text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Paused</p>
                    <p class="text-xl font-bold text-gray-800">{{ $recurringInvoices->where('status', 'paused')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-xl font-bold text-gray-800">{{ $recurringInvoices->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Recurring Invoices Table --}}
    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
                    <th class="p-4 border-b">Title</th>
                    <th class="p-4 border-b">Customer</th>
                    <th class="p-4 border-b">Frequency</th>
                    <th class="p-4 border-b">Amount</th>
                    <th class="p-4 border-b">Next Send</th>
                    <th class="p-4 border-b">Status</th>
                    <th class="p-4 border-b text-center">Sent</th>
                    <th class="p-4 border-b text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recurringInvoices as $recurring)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 border-b">
                            <a href="{{ route('recurring.show', $recurring->id) }}" class="text-blue-600 hover:underline font-semibold">
                                {{ $recurring->title }}
                            </a>
                        </td>
                        <td class="p-4 border-b text-gray-700">{{ $recurring->customer->full_name ?? 'N/A' }}</td>
                        <td class="p-4 border-b">
                            <span class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                {{ ucfirst($recurring->frequency) }}
                            </span>
                        </td>
                        <td class="p-4 border-b font-semibold">{{ $globalSettings->base_currency ?? '$' }}{{ number_format($recurring->amount, 2) }}</td>
                        <td class="p-4 border-b text-gray-600">
                            @if($recurring->next_send_date && $recurring->status === 'active')
                                {{ \Carbon\Carbon::parse($recurring->next_send_date)->format('M d, Y') }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="p-4 border-b">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($recurring->status === 'active') text-green-600 bg-green-50
                                @elseif($recurring->status === 'paused') text-yellow-600 bg-yellow-50
                                @elseif($recurring->status === 'completed') text-blue-600 bg-blue-50
                                @elseif($recurring->status === 'cancelled') text-red-600 bg-red-50
                                @else text-gray-600 bg-gray-100 @endif">
                                {{ ucfirst($recurring->status) }}
                            </span>
                        </td>
                        <td class="p-4 border-b text-center">
                            <span class="text-gray-700 font-medium">{{ $recurring->total_sent ?? 0 }}</span>
                        </td>
                        <td class="p-4 border-b text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('recurring.show', $recurring->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($recurring->status === 'active')
                                    <form action="{{ route('recurring.pause', $recurring->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Pause">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </form>
                                @elseif($recurring->status === 'paused')
                                    <form action="{{ route('recurring.resume', $recurring->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Resume">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('recurring.clone', $recurring->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-gray-700" title="Clone">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>

                                <form action="{{ route('recurring.destroy', $recurring->id) }}" method="POST" class="inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <i class="fas fa-sync-alt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No recurring invoices yet</p>
                            <p class="text-gray-400 text-sm mt-1">Create your first recurring invoice to automate billing.</p>
                            <a href="{{ route('recurring.create') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                <i class="fas fa-plus mr-1"></i> Create Recurring Invoice
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($recurringInvoices->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $recurringInvoices->links('components.pagination') }}
        </div>
    @endif

    {{-- Delete Confirmation --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete Recurring Invoice?',
                    text: 'This action cannot be undone. Generated invoices will not be affected.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
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
