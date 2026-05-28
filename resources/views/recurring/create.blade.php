@extends('layouts.auth.app')

@section('title', 'Create Recurring Invoice - ' . ($globalSettings->company_name ?? config('app.name')))
@php($hideNavbar = true)

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shadow-sm sticky top-0 z-20">
            <div class="flex items-center space-x-4">
                <a href="{{ route('recurring.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-800">Create Recurring Invoice</h2>
            </div>
        </header>

        {{-- Main --}}
        <main class="p-8">
            <div class="max-w-5xl mx-auto space-y-8">

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="recurringForm" action="{{ route('recurring.store') }}" method="POST" class="space-y-8">
                    @csrf

                    {{-- TITLE & SCHEDULE --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">
                            <i class="fas fa-sync-alt text-blue-600 mr-2"></i>Recurring Schedule
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Monthly Website Maintenance" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Frequency *</label>
                                <select name="frequency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                                    <option value="">Select frequency</option>
                                    <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="biweekly" {{ old('frequency') === 'biweekly' ? 'selected' : '' }}>Biweekly (Every 2 weeks)</option>
                                    <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('frequency') === 'quarterly' ? 'selected' : '' }}>Quarterly (Every 3 months)</option>
                                    <option value="yearly" {{ old('frequency') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                                <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date <span class="text-gray-400">(optional)</span></label>
                                <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Occurrences <span class="text-gray-400">(optional)</span></label>
                                <input type="number" name="max_occurrences" value="{{ old('max_occurrences') }}" min="1" placeholder="Leave blank for unlimited" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="auto_send" value="1" {{ old('auto_send', true) ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div>
                                    <span class="font-medium text-gray-800">Auto-send email to customer</span>
                                    <p class="text-sm text-gray-500">Automatically email the invoice to the customer when generated</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- CUSTOMER DETAILS --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Customer Details</h3>
                            <a href="{{ route('customers.create') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                <i class="fas fa-user-plus mr-1"></i> Add New Customer
                            </a>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Customer *</label>
                                <select id="customerSelect" name="customer_id" class="w-full" required></select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                <input type="text" id="customer_first_name" name="first_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" id="customer_last_name" name="last_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                <input type="text" id="customer_company_name" name="company_name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" id="customer_email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                                <input type="text" id="customer_address" name="address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                <input type="text" id="customer_city" name="city" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                <input type="text" id="customer_state" name="state" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code *</label>
                                <input type="text" id="customer_postal_code" name="postal_code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <input type="hidden" name="country" value="US">
                        </div>
                    </div>

                    {{-- PROJECT ADDRESS --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Project Address</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Address</label>
                            <input type="text" id="project_address" name="project_address" placeholder="Start typing the address..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" autocomplete="off">
                            <p class="text-xs text-gray-500 mt-2">Start typing to search for an address — or type it manually.</p>
                        </div>
                    </div>

                    {{-- LINE ITEMS --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Line Items</h3>
                            <button type="button" id="addLineItem" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>Add Line Item
                            </button>
                        </div>

                        <div id="lineItemsContainer" class="space-y-4"></div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex justify-end mb-4">
                                <div class="w-64 text-md text-gray-800">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Discount ($)</label>
                                    <input type="number" step="0.01" id="discount" name="discount" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-right">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <div class="w-64 text-lg font-semibold text-gray-800 flex justify-between">
                                    <span>Total:</span>
                                    <span id="totalAmount">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- NOTES --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Notes</h3>
                        <textarea name="notes" id="notes" rows="4" placeholder="Add any additional notes for this recurring invoice..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">{{ old('notes') }}</textarea>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('recurring.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>Create Recurring Invoice
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    {{-- JS + Select2 --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-selection--single { height:46px !important; border:1px solid #d1d5db !important; border-radius:0.5rem !important; display:flex; align-items:center; }
        .select2-selection__arrow { top:8px !important; right:8px !important; }
        .select2-container { width:100% !important; }
        .select2-results__options { max-height:350px !important; width:100% !important; }
    </style>

    <script>
        $(document).ready(function () {

            // CUSTOMER Select2
            $('#customerSelect').select2({
                placeholder: 'Search customer...',
                ajax: {
                    url: '{{ route("customers.fetch") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params){ return {q: params.term}; },
                    processResults: function(response){
                        if(response.success){
                            return {
                                results: response.data.map(c => ({
                                    id: c.id, text: c.text,
                                    first_name: c.first_name, last_name: c.last_name,
                                    email: c.email, address: c.address, city: c.city,
                                    company_name: c.company_name, country: c.country,
                                    postal_code: c.postal_code, state: c.state
                                }))
                            };
                        }
                        return {results: []};
                    },
                    cache: true
                }
            });

            $('#customerSelect').on('select2:select', function(e){
                const data = e.params.data;
                $('#customer_first_name').val(data.first_name);
                $('#customer_last_name').val(data.last_name);
                $('#customer_company_name').val(data.company_name);
                $('#customer_email').val(data.email);
                $('#customer_address').val(data.address);
                $('#customer_city').val(data.city);
                $('#customer_state').val(data.state);
                $('#customer_postal_code').val(data.postal_code);
            });

            // LINE ITEMS
            const container = document.getElementById('lineItemsContainer');
            const addBtn = document.getElementById('addLineItem');
            const totalDisplay = document.getElementById('totalAmount');

            function updateTotal(){
                let total = 0;
                document.querySelectorAll('.line-total').forEach(el => { total += parseFloat(el.dataset.value||0); });
                const discount = parseFloat(document.getElementById('discount')?.value||0);
                let finalTotal = Math.max(0, total - discount);
                totalDisplay.textContent = `$${finalTotal.toFixed(2)}`;
            }

            function addLineItem(){
                const index = container.children.length;
                const row = document.createElement('div');
                row.className = 'grid grid-cols-12 gap-4 items-center bg-gray-50 p-4 rounded-lg border border-gray-200';
                row.innerHTML = `
                    <div class="col-span-7">
                        <select class="product-select w-full border border-gray-300 rounded-lg"></select>
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="line_items[${index}][unit_price]" step="0.01" class="line-price w-full px-3 py-2 border border-gray-300 rounded-lg text-right" value="0" required>
                        <input type="hidden" class="line-quantity" value="1">
                    </div>
                    <div class="col-span-2">
                        <input type="text" class="line-total w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-right font-semibold text-gray-800" value="$0.00" readonly data-value="0">
                    </div>
                    <div class="col-span-1 text-center">
                        <button type="button" class="remove-line text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </div>
                    <input type="hidden" name="line_items[${index}][product_id]" class="line-product-id">
                    <input type="hidden" name="line_items[${index}][quantity]" value="1">
                    <input type="hidden" name="line_items[${index}][description]" class="line-description">
                `;
                container.appendChild(row);

                const qty = row.querySelector('.line-quantity');
                const price = row.querySelector('.line-price');
                const total = row.querySelector('.line-total');
                const productSelect = row.querySelector('.product-select');
                const desc = row.querySelector('.line-description');
                const productId = row.querySelector('.line-product-id');

                function recalcLineTotal(){
                    const lineTotal = parseFloat(qty.value||0) * parseFloat(price.value||0);
                    total.value = `$${lineTotal.toFixed(2)}`;
                    total.dataset.value = lineTotal;
                    updateTotal();
                }

                qty.addEventListener('input', recalcLineTotal);
                price.addEventListener('input', recalcLineTotal);

                $(productSelect).select2({
                    placeholder: 'Search product...',
                    width: '100%',
                    dropdownParent: $(productSelect).parent(),
                    ajax: {
                        url: '{{ route("products.fetch") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function(params){ return {q: params.term}; },
                        processResults: function(response){
                            if(response.success){
                                return {results: response.data.map(p => ({id: p.id, text: p.name, price: p.price, name: p.name})).concat([{id: 'other', text: 'Other (Custom Product)', price: 0, name: ''}])};
                            }
                            return {results: []};
                        },
                        cache: true
                    }
                });

                $(productSelect).on('select2:select', function(e){
                    const data = e.params.data;
                    if(data.id === 'other'){
                        desc.value = ''; price.value = 0; productId.value = '';
                    } else {
                        desc.value = data.name; price.value = data.price; productId.value = data.id;
                    }
                    recalcLineTotal();
                });

                row.querySelector('.remove-line').addEventListener('click', () => { row.remove(); updateTotal(); });
                recalcLineTotal();
            }

            addBtn.addEventListener('click', addLineItem);
            document.getElementById('discount').addEventListener('input', updateTotal);
            updateTotal();
        });
    </script>
@endsection
