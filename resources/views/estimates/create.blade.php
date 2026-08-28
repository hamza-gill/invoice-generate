@extends('layouts.auth.app')

@section('title', 'Create Estimate - ' . ($globalSettings->company_name ?? config('app.name')))
@php($hideNavbar = true)

@section('content')
    <div class="bg-gray-50">

        {{-- Header --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shadow-sm sticky top-0 z-20">
            <div class="flex items-center space-x-4">
                <a href="{{ route('estimates.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-800">Create New Estimate</h2>
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

                <form id="estimateForm" action="{{ route('estimates.store') }}" method="POST" class="space-y-8">
                    @csrf
                    <input type="hidden" name="action" id="formAction" value="draft">

                    {{-- CUSTOMER DETAILS --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Customer Details</h3>
                        </div>

                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                                <select id="customerSelect" name="customer_id" class="w-full"></select>
                                <p class="text-xs text-gray-500 mt-2">
                                    Search for an existing customer, or choose <span class="font-medium">"+ Add New Customer"</span> to enter their details.
                                </p>
                            </div>
                        </div>

                        <div id="customerDetailsFields" class="grid grid-cols-2 gap-6 hidden">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                <input type="text" id="customer_first_name" name="first_name" value="{{ old('first_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                <input type="text" id="customer_last_name" name="last_name" value="{{ old('last_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                <input type="text" id="customer_company_name" name="company_name" value="{{ old('company_name') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" id="customer_email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="text" id="customer_phone_number" name="phone_number" value="{{ old('phone_number') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                                <input type="text" id="customer_address" name="address" value="{{ old('address') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                <input type="text" id="customer_city" name="city" value="{{ old('city') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State *</label>
                                <input type="text" id="customer_state" name="state" value="{{ old('state') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Zip Code *</label>
                                <input type="text" id="customer_postal_code" name="postal_code" value="{{ old('postal_code') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <select id="customer_country" name="country" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 bg-white">
                                    <option value="">Select country</option>
                                    @include('customers.partials.countries')
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- PROJECT ADDRESS --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Project Address</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Project Address</label>
                            <input type="text" id="project_address" name="project_address" value="{{ old('project_address') }}" placeholder="Start typing the address..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" autocomplete="off">
                            <p class="text-xs text-gray-500 mt-2">Start typing to search for an address — or type it manually.</p>
                        </div>
                    </div>

                    {{-- ESTIMATE DETAILS --}}
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-6">Estimate Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estimate Number *</label>
                                <input type="text" name="estimate_number" value="{{ old('estimate_number', $nextEstimateNumber ?? 'EST-001') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Issue Date *</label>
                                <input type="date" name="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                                <input type="date" name="valid_until" value="{{ old('valid_until', now()->addDays(30)->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            </div>
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
                        <textarea name="notes" id="notes" rows="4" placeholder="Add any additional notes for this estimate..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">{{ old('notes') }}</textarea>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('estimates.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">Cancel</a>
                        <button type="button" id="previewEstimateBtn" class="px-6 py-3 border border-blue-600 text-blue-600 rounded-lg font-semibold hover:bg-blue-50">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </button>
                        <button type="submit" onclick="document.getElementById('formAction').value='draft'" class="px-6 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700">
                            <i class="fas fa-save mr-2"></i>Save as Draft
                        </button>
                        <button type="submit" onclick="document.getElementById('formAction').value='send'" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                            <i class="fas fa-paper-plane mr-2"></i>Save & Send
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    {{-- ESTIMATE PREVIEW MODAL --}}
    <div id="previewModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div id="previewModalBackdrop" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200"></div>

        <div id="previewModalPanel" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[92vh] flex flex-col overflow-hidden opacity-0 scale-95 translate-y-2 transition-all duration-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Estimate Preview</h3>
                        <p class="text-xs text-gray-400">This is a live preview — nothing has been saved yet</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="printPreviewBtn" title="Print" class="w-9 h-9 rounded-lg border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition disabled:opacity-40 disabled:cursor-not-allowed" disabled>
                        <i class="fas fa-print text-sm"></i>
                    </button>
                    <button type="button" id="refreshPreviewBtn" title="Refresh preview" class="w-9 h-9 rounded-lg border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-300 flex items-center justify-center transition">
                        <i class="fas fa-rotate text-sm"></i>
                    </button>
                    <button type="button" id="closePreviewModal" title="Close" class="w-9 h-9 rounded-lg border border-gray-200 text-gray-500 hover:text-red-600 hover:border-red-300 flex items-center justify-center transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 relative bg-gray-100">
                <div id="previewLoading" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 gap-3">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-blue-500"></i>
                    <p class="text-sm">Generating your estimate preview&hellip;</p>
                </div>
                <div id="previewError" class="absolute inset-0 hidden flex-col items-center justify-center text-center gap-3 px-8">
                    <i class="fas fa-triangle-exclamation text-3xl text-red-400"></i>
                    <p class="text-sm text-red-500" id="previewErrorText">Could not generate preview. Please check the form and try again.</p>
                    <button type="button" id="retryPreviewBtn" class="mt-1 px-4 py-2 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100">Try again</button>
                </div>
                <iframe id="previewFrame" class="w-full h-full hidden bg-white" sandbox="allow-same-origin"></iframe>
            </div>
        </div>
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

            // CUSTOMER Select2 — with an inline "+ Add New Customer" option
            const NEW_CUSTOMER_OPTION = {
                id: 'new',
                text: '+ Add New Customer'
            };

            const $customerFields = $('#customerDetailsFields');
            const $requiredCustomerInputs = $('#customer_first_name, #customer_last_name, #customer_email, #customer_address, #customer_city, #customer_state, #customer_postal_code');

            function showCustomerFields(editable){
                $customerFields.removeClass('hidden');
                $requiredCustomerInputs.prop('required', true);
                $('#customer_first_name, #customer_last_name, #customer_company_name, #customer_email, #customer_phone_number, #customer_address, #customer_city, #customer_state, #customer_postal_code, #customer_country')
                    .prop('readonly', !editable);
            }

            function hideCustomerFields(){
                $customerFields.addClass('hidden');
                $requiredCustomerInputs.prop('required', false);
            }

            function clearCustomerFields(){
                $('#customer_first_name, #customer_last_name, #customer_company_name, #customer_email, #customer_phone_number, #customer_address, #customer_city, #customer_state, #customer_postal_code').val('');
                $('#customer_country').val('').trigger('change');
            }

            $('#customer_country').select2({
                placeholder: 'Select country',
                allowClear: true,
                width: '100%'
            });

            $('#customerSelect').select2({
                placeholder: 'Search customer or add a new one...',
                ajax: {
                    url: '{{ route("customers.fetch") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params){ return {q: params.term}; },
                    processResults: function(response){
                        const results = response.success
                            ? response.data.map(c => ({
                                id: c.id, text: c.text,
                                first_name: c.first_name, last_name: c.last_name,
                                email: c.email, phone_number: c.phone_number,
                                address: c.address, city: c.city,
                                company_name: c.company_name, country: c.country,
                                postal_code: c.postal_code, state: c.state
                            }))
                            : [];

                        // Always offer "+ Add New Customer" at the top of the list
                        return { results: [NEW_CUSTOMER_OPTION, ...results] };
                    },
                    cache: true
                }
            });

            $('#customerSelect').on('select2:select', function(e){
                const data = e.params.data;

                if (data.id === 'new') {
                    clearCustomerFields();
                    showCustomerFields(true);
                    $('#customer_first_name').trigger('focus');
                    return;
                }

                $('#customer_first_name').val(data.first_name);
                $('#customer_last_name').val(data.last_name);
                $('#customer_company_name').val(data.company_name);
                $('#customer_email').val(data.email);
                $('#customer_phone_number').val(data.phone_number);
                $('#customer_address').val(data.address);
                $('#customer_city').val(data.city);
                $('#customer_state').val(data.state);
                $('#customer_postal_code').val(data.postal_code);
                $('#customer_country').val(data.country).trigger('change');

                // Existing customer picked — values are already set, no need to show the fields
                hideCustomerFields();
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

            // ---- Modal open/close helpers (animated, Escape + backdrop dismiss, scroll lock) ----
            let openModalId = null;

            function openModal(modalId, backdropId, panelId) {
                const modal = document.getElementById(modalId);
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                requestAnimationFrame(() => {
                    document.getElementById(backdropId).classList.remove('opacity-0');
                    const panel = document.getElementById(panelId);
                    panel.classList.remove('opacity-0', 'scale-95', 'translate-y-2');
                });
                openModalId = modalId;
            }

            function closeModal(modalId, backdropId, panelId, onClosed) {
                document.getElementById(backdropId).classList.add('opacity-0');
                const panel = document.getElementById(panelId);
                panel.classList.add('opacity-0', 'scale-95', 'translate-y-2');
                setTimeout(() => {
                    const modal = document.getElementById(modalId);
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                    if (onClosed) onClosed();
                }, 180);
                openModalId = null;
            }

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && openModalId) {
                    document.getElementById(openModalId).querySelector('[id^="close"]')?.click();
                }
            });

            // ---- ESTIMATE PREVIEW ----
            const previewLoading = document.getElementById('previewLoading');
            const previewError = document.getElementById('previewError');
            const previewErrorText = document.getElementById('previewErrorText');
            const previewFrame = document.getElementById('previewFrame');
            const printPreviewBtn = document.getElementById('printPreviewBtn');

            function setPreviewState(state) {
                previewLoading.classList.toggle('hidden', state !== 'loading');
                previewError.classList.toggle('hidden', state !== 'error');
                previewError.classList.toggle('flex', state === 'error');
                previewFrame.classList.toggle('hidden', state !== 'ready');
                printPreviewBtn.disabled = state !== 'ready';
            }

            function loadEstimatePreview() {
                const form = document.getElementById('estimateForm');
                if (!form.reportValidity()) return false;

                setPreviewState('loading');

                $.ajax({
                    url: '{{ route("estimates.preview") }}',
                    method: 'POST',
                    data: $(form).serialize(),
                    dataType: 'json'
                }).done(function (response) {
                    if (response.success) {
                        previewFrame.srcdoc = response.html;
                        setPreviewState('ready');
                    } else {
                        previewErrorText.textContent = 'Could not generate preview. Please check the form and try again.';
                        setPreviewState('error');
                    }
                }).fail(function () {
                    previewErrorText.textContent = 'Something went wrong while generating the preview. Please try again.';
                    setPreviewState('error');
                });

                return true;
            }

            document.getElementById('previewEstimateBtn').addEventListener('click', function () {
                if (loadEstimatePreview()) {
                    openModal('previewModal', 'previewModalBackdrop', 'previewModalPanel');
                }
            });

            document.getElementById('refreshPreviewBtn').addEventListener('click', loadEstimatePreview);
            document.getElementById('retryPreviewBtn').addEventListener('click', loadEstimatePreview);

            printPreviewBtn.addEventListener('click', function () {
                if (previewFrame.contentWindow) {
                    previewFrame.contentWindow.focus();
                    previewFrame.contentWindow.print();
                }
            });

            document.getElementById('closePreviewModal').addEventListener('click', function () {
                closeModal('previewModal', 'previewModalBackdrop', 'previewModalPanel', function () {
                    previewFrame.src = '';
                    previewFrame.srcdoc = '';
                });
            });

            document.getElementById('previewModalBackdrop').addEventListener('click', function () {
                document.getElementById('closePreviewModal').click();
            });

        });
    </script>
@endsection
