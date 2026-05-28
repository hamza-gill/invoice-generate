@extends('layouts.auth.app')
@section('title', 'Edit Estimate')
@php($hideNavbar = true)
@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col">
    <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shadow-sm sticky top-0 z-20">
        <div class="flex items-center space-x-4">
            <a href="{{ route('estimates.show', $estimate) }}" class="text-gray-600 hover:text-gray-800"><i class="fas fa-arrow-left"></i></a>
            <h2 class="text-xl font-bold text-gray-800">Edit Estimate {{ $estimate->estimate_number }}</h2>
        </div>
    </header>

    <main class="p-8">
        <div class="max-w-5xl mx-auto space-y-8">
            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('estimates.update', $estimate) }}" method="POST" class="space-y-8">
                @csrf @method('PUT')

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Customer</h3>
                    <select name="customer_id" id="customerSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $estimate->customer_id == $c->id ? 'selected' : '' }}>{{ $c->full_name }} ({{ $c->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Estimate Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimate Number</label>
                            <input type="text" value="{{ $estimate->estimate_number }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Issue Date *</label>
                            <input type="date" name="issue_date" value="{{ $estimate->issue_date->format('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                            <input type="date" name="valid_until" value="{{ $estimate->valid_until?->format('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Line Items</h3>
                        <button type="button" id="addLineItem" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"><i class="fas fa-plus mr-2"></i>Add</button>
                    </div>
                    <div id="lineItemsContainer" class="space-y-4">
                        @foreach($estimate->items as $i => $item)
                        <div class="grid grid-cols-12 gap-4 items-center bg-gray-50 p-4 rounded-lg border border-gray-200 line-item-row">
                            <div class="col-span-1 text-center cursor-grab drag-handle"><i class="fas fa-grip-vertical text-gray-400"></i></div>
                            <div class="col-span-6"><input type="text" value="{{ $item->product->name ?? $item->activity }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100" readonly></div>
                            <div class="col-span-2"><input type="number" name="line_items[{{ $i }}][unit_price]" step="0.01" value="{{ $item->amount }}" class="line-price w-full px-3 py-2 border border-gray-300 rounded-lg text-right"></div>
                            <div class="col-span-2"><input type="text" class="line-total w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-right font-semibold" value="${{ number_format($item->quantity * $item->amount, 2) }}" readonly data-value="{{ $item->quantity * $item->amount }}"></div>
                            <div class="col-span-1 text-center"><button type="button" class="remove-line text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></div>
                            <input type="hidden" name="line_items[{{ $i }}][product_id]" value="{{ $item->product_id }}">
                            <input type="hidden" name="line_items[{{ $i }}][quantity]" value="{{ $item->quantity }}">
                            <input type="hidden" name="line_items[{{ $i }}][description]" value="{{ $item->product->name ?? $item->activity }}">
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="flex justify-end mb-4">
                            <div class="w-64">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Discount ($)</label>
                                <input type="number" step="0.01" id="discount" name="discount" value="{{ $estimate->discount }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-right">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <div class="w-64 text-lg font-semibold text-gray-800 flex justify-between">
                                <span>Total:</span><span id="totalAmount">${{ number_format($estimate->amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Notes</h3>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('notes', $estimate->notes) }}</textarea>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('estimates.show', $estimate) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">Update Estimate</button>
                </div>
            </form>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.querySelectorAll('.remove-line').forEach(btn => btn.addEventListener('click', function(){ this.closest('.grid').remove(); }));
new Sortable(document.getElementById('lineItemsContainer'), { handle:'.drag-handle', animation:150 });
</script>
@endsection
