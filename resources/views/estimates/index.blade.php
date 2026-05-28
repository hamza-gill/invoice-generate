@extends('layouts.auth.app')

@section('title', 'Estimates - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Estimates</h1>
            <p class="text-gray-500 mt-1">Create and manage estimates for your clients</p>
        </div>
        <a href="{{ route('estimates.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Create Estimate
        </a>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <input type="text" id="searchEstimate"
               placeholder="Search by estimate number, client, or status..."
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
    </div>

    {{-- Estimates Table --}}
    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
                    <th class="p-4 border-b">Estimate #</th>
                    <th class="p-4 border-b">Customer</th>
                    <th class="p-4 border-b">Amount</th>
                    <th class="p-4 border-b">Issue Date</th>
                    <th class="p-4 border-b">Valid Until</th>
                    <th class="p-4 border-b">Status</th>
                    <th class="p-4 border-b text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="estimateTableBody">
                @forelse($estimates as $estimate)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 border-b font-semibold">
                            <a href="{{ route('estimates.show', $estimate->id) }}" class="text-blue-600 hover:underline">
                                #{{ $estimate->estimate_number }}
                            </a>
                        </td>
                        <td class="p-4 border-b text-gray-700">{{ $estimate->customer->full_name ?? 'N/A' }}</td>
                        <td class="p-4 border-b font-semibold">{{ $globalSettings->base_currency ?? '$' }}{{ number_format($estimate->amount, 2) }}</td>
                        <td class="p-4 border-b text-gray-600">{{ \Carbon\Carbon::parse($estimate->issue_date)->format('M d, Y') }}</td>
                        <td class="p-4 border-b text-gray-600">
                            @if($estimate->valid_until)
                                {{ \Carbon\Carbon::parse($estimate->valid_until)->format('M d, Y') }}
                                @if(\Carbon\Carbon::parse($estimate->valid_until)->isPast() && !in_array($estimate->status, ['approved', 'converted', 'declined']))
                                    <span class="text-xs text-red-500 ml-1">(Expired)</span>
                                @endif
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="p-4 border-b">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($estimate->status === 'draft') text-gray-600 bg-gray-100
                                @elseif($estimate->status === 'sent') text-blue-600 bg-blue-50
                                @elseif($estimate->status === 'viewed') text-purple-600 bg-purple-50
                                @elseif($estimate->status === 'approved') text-green-600 bg-green-50
                                @elseif($estimate->status === 'declined') text-red-600 bg-red-50
                                @elseif($estimate->status === 'converted') text-indigo-600 bg-indigo-50
                                @elseif($estimate->status === 'expired') text-orange-600 bg-orange-50
                                @else text-gray-600 bg-gray-100 @endif">
                                {{ ucfirst($estimate->status) }}
                            </span>
                        </td>
                        <td class="p-4 border-b text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('estimates.show', $estimate->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(in_array($estimate->status, ['draft']))
                                    <a href="{{ route('estimates.edit', $estimate->id) }}" class="text-gray-600 hover:text-gray-800" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif

                                @if(in_array($estimate->status, ['draft', 'sent', 'viewed']))
                                    <form action="{{ route('estimates.send', $estimate->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800" title="Send">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($estimate->status === 'approved')
                                    <form action="{{ route('estimates.convert', $estimate->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-800" title="Convert to Invoice">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('estimates.destroy', $estimate->id) }}" method="POST" class="inline delete-form">
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
                        <td colspan="7" class="text-center py-12">
                            <i class="fas fa-file-alt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 text-lg">No estimates yet</p>
                            <p class="text-gray-400 text-sm mt-1">Create your first estimate to send proposals to your clients.</p>
                            <a href="{{ route('estimates.create') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                <i class="fas fa-plus mr-1"></i> Create Estimate
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($estimates->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $estimates->links('components.pagination') }}
        </div>
    @endif

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete Estimate?',
                    text: 'This action cannot be undone.',
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

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchEstimate');
            const rows = document.querySelectorAll('#estimateTableBody tr');

            searchInput.addEventListener('keyup', function() {
                const query = this.value.toLowerCase();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
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
