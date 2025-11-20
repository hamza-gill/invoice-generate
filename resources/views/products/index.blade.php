@extends('layouts.auth.app')

@section('title', 'Products - ' . config('app.name', 'ReconX'))

@section('content')
    <div class="w-full max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Products</h1>

            @can('create', App\Models\Product::class)
                <div class="space-x-2">
                    <button id="importCsvBtn" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-file-import mr-2"></i> Import CSV
                    </button>
                    <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Add Product
                    </a>
                </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Products Table -->
        <div class="bg-white shadow rounded-xl border border-gray-100 overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                <tr class="bg-gray-50 text-left text-gray-600 uppercase text-sm">
                    <th class="p-4 border-b">Name</th>
                    <th class="p-4 border-b">Category</th>
                    <th class="p-4 border-b">Price</th>
                    <th class="p-4 border-b">Status</th>
                    <th class="p-4 border-b text-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 border-b">{{ $product->name }}</td>
                        <td class="p-4 border-b">{{ $product->category ?? 'N/A' }}</td>
                        <td class="p-4 border-b">${{ number_format($product->price, 2) }}</td>
                        <td class="p-4 border-b">
                            @if($product->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Active</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Disabled</span>
                            @endif
                        </td>
                        <td class="p-4 border-b text-right space-x-2">
                            @can('view', $product)
                                <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:underline">View</a>
                            @endcan

                            @can('update', $product)
                                <a href="{{ route('products.edit', $product->id) }}" class="text-yellow-600 hover:underline">Edit</a>
                            @endcan

                            @can('delete', $product)
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-6 text-gray-500">No products found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex justify-center">
            {{ $products->links('components.pagination') }}
        </div>

        <!-- Import CSV Modal -->
        @can('import', App\Models\Product::class)
            <div id="importCsvModal"
                 class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-8 rounded-xl w-full max-w-md shadow-lg">
                    <h2 class="text-lg font-semibold mb-4">Import Products (CSV)</h2>
                    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
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
    </div>

    @can('import', App\Models\Product::class)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Import Modal Control
                const modal = document.getElementById('importCsvModal');
                const openModalBtn = document.getElementById('importCsvBtn');
                const closeModalBtn = document.getElementById('closeImportModal');

                if (openModalBtn && closeModalBtn && modal) {
                    openModalBtn.addEventListener('click', () => modal.classList.remove('hidden'));
                    closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
                }
            });
        </script>
    @endcan
@endsection
