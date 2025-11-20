@extends('layouts.auth.app')

@section('title', 'Customers - ReconX')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Customers</h1>
        @include('layouts.errors')

        @can('create', App\Models\Customer::class)
            <div class="space-x-2">
                <button id="importCsvBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-file-import mr-2"></i> Import CSV
                </button>
                <a href="{{ route('customers.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-user-plus mr-2"></i> Add Customer
                </a>
            </div>
        @endcan
    </div>

    <!-- Search -->
    @can('search', App\Models\Customer::class)
        <div class="mb-4">
            <input type="text" id="searchCustomer"
                   placeholder="Search by name, email, company, or address..."
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-600">
        </div>
    @endcan

    <!-- Customer Table -->
    <div class="bg-white shadow rounded-xl border border-gray-100 overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
            <tr class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
                <th class="p-4 border-b">Name</th>
                <th class="p-4 border-b">Email</th>
                <th class="p-4 border-b">Company</th>
                <th class="p-4 border-b">Address</th>
                <th class="p-4 border-b">Country</th>
                <th class="p-4 border-b">Invoices</th>
                <th class="p-4 border-b text-right">Actions</th>
            </tr>
            </thead>
            <tbody id="customerTableBody">
            @forelse($customers as $customer)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4 border-b">{{ $customer->name }}</td>
                    <td class="p-4 border-b">{{ $customer->email }}</td>
                    <td class="p-4 border-b">{{ $customer->company_name ?? 'N/A' }}</td>
                    <td class="p-4 border-b">{{ $customer->address ?? 'N/A' }}</td>
                    <td class="p-4 border-b">{{ $customer->country ?? 'N/A' }}</td>
                    <td class="p-4 border-b">{{ $customer->invoices->count() }}</td>
                    <td class="p-4 border-b text-right">
                        @can('view', $customer)
                            <a href="{{ route('customers.show', $customer->id) }}" class="text-blue-600 hover:underline">View</a>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-6 text-gray-500">No customers found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
        {{ $customers->links('components.pagination') }}
    </div>

    <!-- Import CSV Modal -->
    @can('import', App\Models\Customer::class)
        <div id="importCsvModal"
             class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-lg">
                <h2 class="text-lg font-semibold mb-4">Import Customers (CSV)</h2>
                <form action="{{ route('customers.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" accept=".csv" required
                           class="w-full border rounded-lg p-2 mb-4">
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="closeImportModal"
                                class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @can('import', App\Models\Customer::class)
            // Import Modal Control
            const modal = document.getElementById('importCsvModal');
            const openModalBtn = document.getElementById('importCsvBtn');
            const closeModalBtn = document.getElementById('closeImportModal');

            if (openModalBtn && closeModalBtn && modal) {
                openModalBtn.addEventListener('click', () => modal.classList.remove('hidden'));
                closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
            }
            @endcan

            @can('search', App\Models\Customer::class)
            // AJAX Search
            const searchInput = document.getElementById('searchCustomer');
            const tableBody = document.getElementById('customerTableBody');
            const canViewCustomer = @json(auth()->user()->can('viewAny', App\Models\Customer::class));
            let timeout = null;

            if (searchInput && tableBody) {
                searchInput.addEventListener('keyup', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        const query = this.value.trim();

                        // Show loading state
                        tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500">Searching...</td>
                        </tr>`;

                        fetch(`{{ route('customers.search') }}?query=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                const customers = data.data;
                                if (!customers || customers.length === 0) {
                                    tableBody.innerHTML = `
                                    <tr>
                                        <td colspan="7" class="text-center py-6 text-gray-500">
                                            No matching customers found.
                                        </td>
                                    </tr>`;
                                    return;
                                }

                                tableBody.innerHTML = customers.map(c => {
                                    const viewLink = canViewCustomer
                                        ? `<a href="/customers/${c.id}" class="text-blue-600 hover:underline">View</a>`
                                        : '';

                                    return `
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="p-4 border-b">${c.name ?? 'N/A'}</td>
                                            <td class="p-4 border-b">${c.email ?? 'N/A'}</td>
                                            <td class="p-4 border-b">${c.company_name ?? 'N/A'}</td>
                                            <td class="p-4 border-b">${c.address ?? 'N/A'}</td>
                                            <td class="p-4 border-b">${c.country ?? 'N/A'}</td>
                                            <td class="p-4 border-b">${c.invoices_count ?? 0}</td>
                                            <td class="p-4 border-b text-right">
                                                ${viewLink}
                                            </td>
                                        </tr>
                                    `;
                                }).join('');
                            })
                            .catch(() => {
                                tableBody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-red-500">
                                        Error loading data.
                                    </td>
                                </tr>`;
                            });
                    }, 300);
                });
            }
            @endcan
        });
    </script>
@endsection
