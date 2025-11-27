@extends('layouts.auth.app')

@section('title', 'Add Product - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="w-full max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Add New Product</h1>
                <p class="text-gray-500 mt-1">Fill in the product details below to add it to your catalog.</p>
            </div>
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <form action="{{ route('products.store') }}" method="POST"
              class="bg-white p-8 rounded-2xl shadow-md border border-gray-100 transition-all hover:shadow-lg">
            @csrf

            {{-- ✅ Display Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Product Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1 text-blue-600"></i> Product Name
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400"
                           placeholder="Enter product name" required>
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-list mr-1 text-blue-600"></i> Category
                    </label>
                    <input type="text" name="category" value="{{ old('category') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400"
                           placeholder="Enter category name" required>
                </div>

                {{-- Price --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign mr-1 text-blue-600"></i> Price
                    </label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400"
                           placeholder="e.g. 199.99" required>
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on mr-1 text-blue-600"></i> Active Status
                    </label>
                    <select name="is_active"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                        <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left mr-1 text-blue-600"></i> Description
                </label>
                <textarea name="description" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent placeholder-gray-400 resize-none"
                          placeholder="Write a short description about this product...">{{ old('description') }}</textarea>
            </div>

            {{-- Buttons --}}
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('products.index') }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition">
                    <i class="fas fa-save mr-2"></i> Save Product
                </button>
            </div>
        </form>
    </div>
@endsection
