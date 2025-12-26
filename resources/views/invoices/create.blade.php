@extends('layouts.auth.app')

@section('title', 'Create Invoice - '.($globalSettings->company_name ?? config('app.name')))
@php($hideNavbar = true)

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shadow-sm sticky top-0 z-20">
            <div class="flex items-center space-x-4">
                <a href="{{ route('invoices.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-800">Create New Invoice</h2>
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

                {{-- FORM --}}
                <form id="invoiceForm" action="{{ route('invoices.store') }}" method="POST" class="space-y-8">
                    @csrf

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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                                <select id="customerSelect" name="customer_id" class="w-full"></select>
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
                            <p class="text-xs text-gray-500 mt-2">
                                Start typing to search for an address in the U.S. — or type it manually if it's not listed.
                            </p>
                        </div>
                    </div>

                    {{-- INVOICE DETAILS --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Invoice Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Invoice Number *</label>
                                <input type="text" name="invoice_number" value="{{ old('invoice_number', $globalSettings->starting_invoice_number ?? '') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Issue Date *</label>
                                <input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            @if(!empty($globalSettings->enable_due_date) && $globalSettings->enable_due_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Due Date *</label>
                                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" min="{{ now()->format('Y-m-d') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- RUSH DELIVERY ENABLE (SIMPLE CHECKBOX) --}}
                    {{-- RUSH DELIVERY ENABLE --}}
                    @if($globalSettings->hasRushDelivery())
                        <div class="bg-yellow-50 border border-yellow-300 rounded-2xl shadow-sm p-6">
                            <div class="flex items-start space-x-4">

                                <div class="flex items-center h-5">
                                    <input type="checkbox"
                                           id="enable_rush_delivery"
                                           name="enable_rush_delivery"
                                           value="1"
                                           {{ old('enable_rush_delivery') ? 'checked' : '' }}
                                           class="w-5 h-5 text-yellow-600 bg-white border-gray-300 rounded focus:ring-yellow-500 focus:ring-2">
                                </div>

                                <div class="flex-1">
                                    <label for="enable_rush_delivery" class="font-semibold text-gray-800 cursor-pointer">
                                        <i class="fas fa-shipping-fast mr-2 text-yellow-600"></i>Enable Rush Delivery Options
                                    </label>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Customer will be able to select rush delivery options when accepting the invoice payment.
                                    </p>
                                    <div class="mt-3 p-3 bg-white rounded-lg border border-yellow-200">
                                        <p class="text-xs text-gray-700 font-medium mb-2">Available options:</p>
                                        <ul class="text-xs text-gray-600 space-y-1">
                                            @foreach ($globalSettings->rush_options as $option)
                                                <li class="flex justify-between">
                                                    <span>
                                                        • {{ $option['label'] }}<br>
                                                        <small class="text-gray-500">
                                                            Delivery by:
                                                            <strong>

                                                                {{
                                                                    \Carbon\Carbon::today()->copy()->addWeekdays(
                                                                        1 + (
                                                                            is_numeric($option['days'])
                                                                                ? (int)$option['days'] - 1
                                                                                : (preg_match('/^(\d+)-(\d+)$/', $option['days'], $m) ? (int)$m[2] - 1
                                                                                    : ($option['days'] === 'standard' ? 7 - 1 : 0)
                                                                                  )
                                                                        )
                                                                    )->format('M d, Y')
                                                                }}
                                                            </strong>
                                                        </small>
                                                    </span>
                                                    <span class="font-semibold {{ $option['fee'] > 0 ? 'text-yellow-700' : 'text-green-600' }}">
                                                        {{ $option['fee'] > 0 ? '+$' . number_format($option['fee'], 2) : 'FREE' }}
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

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
                        <textarea name="notes" id="notes" rows="4" placeholder="Add any additional notes or instructions for this invoice..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"></textarea>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('invoices.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">Create Invoice</button>
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
                                    id: c.id,
                                    text: c.text,
                                    first_name: c.first_name,
                                    last_name: c.last_name,
                                    email: c.email,
                                    address: c.address,
                                    city: c.city,
                                    company_name: c.company_name,
                                    country: c.country,
                                    postal_code: c.postal_code,
                                    state: c.state
                                }))
                            };
                        }
                        return {results: []};
                    },
                    cache:true
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
                let finalTotal = total - discount;
                if(finalTotal<0) finalTotal=0;
                totalDisplay.textContent=`$${finalTotal.toFixed(2)}`;
            }

            function addLineItem(){
                const index = container.children.length;

                const row = document.createElement('div');
                row.className='grid grid-cols-12 gap-4 items-center bg-gray-50 p-4 rounded-lg border border-gray-200';
                row.innerHTML=`
            <div class="col-span-7">
                <select class="product-select w-full border border-gray-300 rounded-lg"></select>
            </div>

            <div class="col-span-2">
                <input type="number"  name="line_items[${index}][unit_price]"  step="0.01" class="line-price w-full px-3 py-2 border border-gray-300 rounded-lg text-right" value="0" required>
                <input type="hidden" class="line-quantity w-full px-3 py-2 border border-gray-300 rounded-lg text-right" value="1" min="1" required>

            </div>

            <div class="col-span-2">
                <input type="text" class="line-total w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-right font-semibold text-gray-800" value="$0.00" readonly data-value="0">
            </div>

            <div class="col-span-1 text-center">
                <button type="button" class="remove-line text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
            </div>

            <input type="hidden" name="line_items[${index}][product_id]" class="line-product-id">
            <input type="hidden" name="line_items[${index}][quantity]"  value="1" class="line-product-id">
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
                    const lineTotal = parseFloat(qty.value||0)*parseFloat(price.value||0);
                    total.value=`$${lineTotal.toFixed(2)}`;
                    total.dataset.value=lineTotal;
                    updateTotal();
                }

                qty.addEventListener('input', recalcLineTotal);
                price.addEventListener('input', recalcLineTotal);

                $(productSelect).select2({
                    placeholder:'Search product...',
                    width:'100%',
                    dropdownParent: $(productSelect).parent(),
                    ajax:{
                        url:'{{ route("products.fetch") }}',
                        dataType:'json',
                        delay:250,
                        data:function(params){return {q:params.term};},
                        processResults:function(response){
                            if(response.success){
                                return {results: response.data.map(p=>({id:p.id, text:p.name, price:p.price, name:p.name})).concat([{id:'other', text:'Other (Custom Product)', price:0, name:''}])};
                            }
                            return {results:[]};
                        },
                        cache:true
                    }
                });

                $(productSelect).on('select2:select', function(e){
                    const data = e.params.data;
                    if(data.id==='other'){
                        desc.value=''; price.value=0; productId.value=''; desc.removeAttribute('readonly');
                    }else{
                        desc.value=data.name; price.value=data.price; productId.value=data.id; desc.setAttribute('readonly', true);
                    }
                    recalcLineTotal();
                });

                row.querySelector('.remove-line').addEventListener('click', ()=>{row.remove(); updateTotal();});
                recalcLineTotal();
            }

            addBtn.addEventListener('click', addLineItem);
            document.getElementById('discount').addEventListener('input', updateTotal);
            updateTotal();

        });

        // Google Places Autocomplete
        function initAutocomplete(){
            function attachAutocomplete(inputId){
                const input=document.getElementById(inputId);
                if(!input) return;

                const autocomplete=new google.maps.places.Autocomplete(input,{
                    types:['address'],
                    componentRestrictions:{country:'us'},
                    fields:['formatted_address']
                });

                autocomplete.addListener('place_changed', function(){
                    const place = autocomplete.getPlace();
                    if(place && place.formatted_address){
                        input.value = place.formatted_address;
                    }
                });
            }

            attachAutocomplete('project_address');
            attachAutocomplete('customer_address');
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{$globalSettings->google_places_key}}&libraries=places&callback=initAutocomplete" async defer></script>
@endsection
