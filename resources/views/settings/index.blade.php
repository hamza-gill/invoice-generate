@extends('layouts.auth.app')

@section('title', 'Settings - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    @can('view', $setting)
        <div class="max-w-7xl mx-auto">
            <!-- 🔹 Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
                <p class="text-gray-500 mt-1">Manage your organization, integrations, and preferences.</p>
            </div>

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-2xl flex overflow-hidden">
                <!-- 🔸 Sidebar Tabs -->
                <div class="w-64 bg-gray-50 border-r border-gray-200">
                    <nav class="flex flex-col py-6">
                        <button id="tab-org"
                                class="tab-btn flex items-center px-6 py-3 text-left font-medium text-blue-600 bg-blue-50 border-l-4 border-blue-600">
                            <i class="fas fa-building mr-3 text-blue-600"></i> Organization
                        </button>

                        <button id="tab-int"
                                class="tab-btn flex items-center px-6 py-3 text-left text-gray-600 hover:bg-gray-100 hover:text-blue-600 border-l-4 border-transparent">
                            <i class="fas fa-plug mr-3"></i> Integrations
                        </button>

                        <button id="tab-invoice"
                                class="tab-btn flex items-center px-6 py-3 text-left text-gray-600 hover:bg-gray-100 hover:text-blue-600 border-l-4 border-transparent">
                            <i class="fas fa-file-invoice-dollar mr-3"></i> Invoice Configuration
                        </button>

                        @can('view', App\Models\WebhookSetting::class)
                            <button id="tab-webhook"
                                    class="tab-btn flex items-center px-6 py-3 text-left text-gray-600 hover:bg-gray-100 hover:text-blue-600 border-l-4 border-transparent">
                                <i class="fas fa-link mr-3"></i> Webhook Settings
                            </button>
                        @endcan

                        <button id="tab-security"
                                class="tab-btn flex items-center px-6 py-3 text-left text-gray-600 hover:bg-gray-100 hover:text-blue-600 border-l-4 border-transparent">
                            <i class="fas fa-shield-alt mr-3"></i> Security
                        </button>
                    </nav>
                </div>

                <!-- 🔹 Main Content -->
                <div class="flex-1 p-8">
                    {{-- 🏢 Organization Settings --}}
                    <div id="tab-content-org">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Organization Settings</h2>

                        @cannot('updateOrganization', $setting)
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                <i class="fas fa-info-circle mr-2"></i> You have read-only access to these settings.
                            </div>
                        @endcannot

                        <form method="POST" action="{{ route('settings.organization.update') }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Company Name</label>
                                    <input type="text" name="company_name"
                                           value="{{ old('company_name', $setting->company_name) }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Company Email</label>
                                    <input type="email" name="contact_email"
                                           value="{{ old('contact_email', $setting->contact_email) }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="info@company.com"
                                        {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Country</label>
                                    <input type="text" name="country"
                                           value="{{ old('country', $setting->country) }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>
                                </div>

                                <!-- 🌍 Base Currency Dropdown -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Base Currency</label>
                                    <select name="base_currency"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                        {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>
                                        <option value="">-- Select Currency --</option>
                                        <option value="$" {{ old('base_currency', $setting->base_currency) === '$' ? 'selected' : '' }}>🇺🇸 USD — $</option>
                                        <option value="€" {{ old('base_currency', $setting->base_currency) === '€' ? 'selected' : '' }}>🇪🇺 EUR — €</option>
                                        <option value="£" {{ old('base_currency', $setting->base_currency) === '£' ? 'selected' : '' }}>🇬🇧 GBP — £</option>
                                        <option value="₹" {{ old('base_currency', $setting->base_currency) === '₹' ? 'selected' : '' }}>🇮🇳 INR — ₹</option>
                                        <option value="C$" {{ old('base_currency', $setting->base_currency) === 'C$' ? 'selected' : '' }}>🇨🇦 CAD — C$</option>
                                        <option value="A$" {{ old('base_currency', $setting->base_currency) === 'A$' ? 'selected' : '' }}>🇦🇺 AUD — A$</option>
                                        <option value="¥" {{ old('base_currency', $setting->base_currency) === '¥' ? 'selected' : '' }}>🇯🇵 JPY — ¥</option>
                                        <option value="Fr" {{ old('base_currency', $setting->base_currency) === 'Fr' ? 'selected' : '' }}>🇨🇭 CHF — Fr</option>
                                        <option value="NZ$" {{ old('base_currency', $setting->base_currency) === 'NZ$' ? 'selected' : '' }}>🇳🇿 NZD — NZ$</option>
                                        <option value="S$" {{ old('base_currency', $setting->base_currency) === 'S$' ? 'selected' : '' }}>🇸🇬 SGD — S$</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Tax Percentage</label>
                                    <input type="number" step="0.01" name="tax_percentage"
                                           value="{{ old('tax_percentage', $setting->tax_percentage ?? '') }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Company Logo</label>

                                    <input type="file" name="logo_path" accept="image/*" id="logo-input"
                                           class="w-full border border-gray-300 rounded-lg p-2.5"
                                        {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>

                                    <div class="mt-3">
                                        @if(!empty($setting->logo_path) && file_exists(public_path('storage/' . $setting->logo_path)))
                                            <img id="logo-preview"
                                                 src="{{ asset('storage/' . $setting->logo_path) }}"
                                                 alt="Company Logo"
                                                 class="w-24 h-24 object-contain border rounded-lg shadow-sm">
                                        @else
                                            <img id="logo-preview" src="" alt="Preview"
                                                 class="hidden w-24 h-24 object-contain border rounded-lg shadow-sm">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Address</label>
                                <textarea name="address" rows="3"
                                          class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>{{ old('address', $setting->address) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Invoice Notes</label>
                                <textarea name="invoice_notes" rows="3"
                                          class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>{{ old('invoice_notes', $setting->invoice_notes) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Terms & Conditions</label>
                                <textarea name="invoice_terms" rows="4"
                                          class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          {{ !Gate::allows('updateOrganization', $setting) ? 'disabled' : '' }}>{{ old('invoice_terms', $setting->invoice_terms) }}</textarea>
                            </div>

                            @can('updateOrganization', $setting)
                                <button type="submit"
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition-all">
                                    Save Changes
                                </button>
                            @endcan
                        </form>
                    </div>

                    {{-- 🔌 Integration Settings --}}
                    <div id="tab-content-int" class="hidden">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Integrations</h2>

                        @cannot('updateIntegration', $setting)
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                <i class="fas fa-info-circle mr-2"></i> You have read-only access to these settings.
                            </div>
                        @endcannot

                        <form method="POST" action="{{ route('settings.integration.update') }}" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @php
                                    $userRole = auth()->user()->role ?? 'manager';
                                    $isAdminOrDeveloper = in_array($userRole, ['admin', 'developer']);

                                    // Mask Stripe Public Key (for ALL users)
                                    $stripePublicKey = $setting->stripe_public_key ?? '';
                                    $maskedPublicKey = $stripePublicKey
                                        ? str_repeat('*', max(0, strlen($stripePublicKey) - 4)) . substr($stripePublicKey, -4)
                                        : '';

                                    // Mask Stripe Secret Key (for ALL users)
                                    $stripeSecretKey = $setting->stripe_secret_key ?? '';
                                    $maskedSecretKey = $stripeSecretKey
                                        ? str_repeat('*', max(0, strlen($stripeSecretKey) - 4)) . substr($stripeSecretKey, -4)
                                        : '';

                                    // Mask Webhook URL (for ALL users)
                                    $webhookUrl = $webhookUrl  ?? '';
                                    $maskedWebhookUrl = $webhookUrl
                                        ? str_repeat('*', max(0, strlen($webhookUrl) - 4)) . substr($webhookUrl, -4)
                                        : '';

                                    // Mask Webhook Secret (for ALL users)
                                    $webhookSecret = $setting->webhook_secret ?? '';
                                    $maskedWebhookSecret = $webhookSecret
                                        ? str_repeat('*', max(0, strlen($webhookSecret) - 4)) . substr($webhookSecret, -4)
                                        : '';

                                    // Mask Google Places Key (for ALL users)
                                    $googleKey = $setting->google_places_key ?? '';
                                    $maskedGoogleKey = $googleKey
                                        ? str_repeat('*', max(0, strlen($googleKey) - 4)) . substr($googleKey, -4)
                                        : '';
                                @endphp

                                    <!-- Stripe Public Key -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Stripe Public Key</label>
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="password"
                                            id="stripe_public_key"
                                            name="stripe_public_key"
                                            value="{{ old('stripe_public_key', $maskedPublicKey) }}"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                            placeholder="pk_live_xxxxx"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    id="toggleStripePublicKey"
                                                    data-full-key="{{ $stripePublicKey }}"
                                                    class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>

                                <!-- Stripe Secret Key -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Stripe Secret Key</label>
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="password"
                                            id="stripe_secret_key"
                                            name="stripe_secret_key"
                                            value="{{ old('stripe_secret_key', $maskedSecretKey) }}"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                            placeholder="sk_live_xxxxx"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    id="toggleStripeSecretKey"
                                                    data-full-key="{{ $stripeSecretKey }}"
                                                    class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>

                                <!-- Webhook URL -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook URL</label>
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="password"
                                            id="webhook_url"
                                            readonly
                                            value="{{ $maskedWebhookUrl }}"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-100 cursor-not-allowed">

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    id="toggleWebhookUrl"
                                                    data-full-key="{{ $webhookUrl }}"
                                                    class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                        @endif

                                        <button type="button"
                                                onclick="copyWebhook('{{ $webhookUrl }}')"
                                                class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition">
                                            Copy
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>


                                <!-- Webhook Secret -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook Secret</label>
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="password"
                                            id="integration_webhook_secret"
                                            name="webhook_secret"
                                            value="{{ old('webhook_secret', $maskedWebhookSecret) }}"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    id="toggleIntegrationWebhookSecret"
                                                    data-full-key="{{ $webhookSecret }}"
                                                    class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>

                                <!-- Google Places API Key -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Google Places API Key</label>
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="password"
                                            id="google_places_key"
                                            name="google_places_key"
                                            value="{{ old('google_places_key', $maskedGoogleKey) }}"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                            placeholder="Enter your Google Places API Key"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    id="toggleGoogleKey"
                                                    data-full-key="{{ $googleKey }}"
                                                    class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>
                            </div>

                            @can('updateIntegration', $setting)
                                <button type="submit"
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition-all">
                                    Save Integration
                                </button>
                            @endcan
                        </form>
                    </div>

                    {{-- 🧾 Invoice Configuration --}}
                    <div id="tab-content-invoice" class="hidden">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Invoice Configuration</h2>

                        @cannot('updateInvoice', $setting)
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                <i class="fas fa-info-circle mr-2"></i> You have read-only access to these settings.
                            </div>
                        @endcannot

                        <form method="POST" action="{{ route('settings.invoice.update') }}" class="space-y-6">
                            @csrf

                            @php
                                $userRole = auth()->user()->role ?? 'manager';
                                $isAdminOrDeveloper = in_array($userRole, ['admin', 'developer']);

                                // Mask Tax ID
                                $taxId = $setting->tax_id ?? '';
                                $maskedTaxId = $taxId
                                    ? str_repeat('*', max(0, strlen($taxId) - 4)) . substr($taxId, -4)
                                    : '';

                                // Mask Starting Invoice Number
                                $invoiceNumber = $setting->starting_invoice_number ?? 'INV-' . date('Y') . '-001';
                                $maskedInvoiceNumber = $invoiceNumber
                                    ? str_repeat('*', max(0, strlen($invoiceNumber) - 4)) . substr($invoiceNumber, -4)
                                    : '';
                            @endphp

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Tax ID</label>
                                <div class="flex items-center space-x-3">
                                    <input
                                        type="password"
                                        id="tax_id_invoice"
                                        name="tax_id_invoice"
                                        value="{{ old('tax_id_invoice', $maskedTaxId) }}"
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="123-456-789"
                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>

                                    @if($isAdminOrDeveloper)
                                        <button type="button"
                                                id="toggleTaxId"
                                                data-full-key="{{ $taxId }}"
                                                class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Starting Invoice Number</label>
                                <div class="flex items-center space-x-3">
                                    <input
                                        type="password"
                                        id="starting_invoice_number"
                                        name="starting_invoice_number"
                                        value="{{ old('starting_invoice_number', $maskedInvoiceNumber) }}"
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="INV-2025-001"
                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>

                                    @if($isAdminOrDeveloper)
                                        <button type="button"
                                                id="toggleInvoiceNumber"
                                                data-full-key="{{ $invoiceNumber }}"
                                                class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    Only the last 4 characters are shown by default. Set the starting invoice number. The next invoices will auto-increment from this number.
                                    <br>Format: <code>INV-YYYY-NNN</code> (e.g., <code>INV-{{ date('Y') }}-001</code>)
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700 font-medium">Enable Terms & Conditions</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_terms" value="1" class="sr-only peer"
                                            {{ old('enable_terms', $setting->enable_terms) ? 'checked' : '' }}
                                            {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700 font-medium">Enable Invoice Notes</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_invoice_notes" value="1" class="sr-only peer"
                                            {{ old('enable_invoice_notes', $setting->enable_invoice_notes) ? 'checked' : '' }}
                                            {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700 font-medium">Enable Due Date</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_due_date" value="1" class="sr-only peer"
                                            {{ old('enable_due_date', $setting->enable_due_date) ? 'checked' : '' }}
                                            {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-700 font-medium">Enable Tax</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_tax" value="1" class="sr-only peer"
                                            {{ old('enable_tax', $setting->enable_tax) ? 'checked' : '' }}
                                            {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-gray-700 font-medium">Enable Tax ID</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_tax_id" value="1" class="sr-only peer"
                                            {{ old('enable_tax_id', $setting->enable_tax_id) ? 'checked' : '' }}
                                            {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                    </label>
                                </div>

                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-gray-700 font-medium">Enable Rush Delivery</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="enable_rush_delivery" value="1"
                                               id="enable_rush_delivery_toggle"
                                               class="sr-only peer"
                                            {{ old('enable_rush_delivery', $setting->enable_rush_delivery) ? 'checked' : '' }}
                                            {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                        <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                    </label>
                                </div>
                            </div>

                            {{-- 🚀 Rush Delivery Options Configuration --}}
                            <div id="rush_delivery_section" class="mt-8 p-6 bg-gray-50 border border-gray-200 rounded-lg {{ old('enable_rush_delivery', $setting->enable_rush_delivery) ? '' : 'hidden' }}">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-xl font-semibold text-gray-800">Rush Delivery Options</h3>
                                    <button type="button"
                                            id="add_rush_option"
                                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all"
                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                        <i class="fas fa-plus mr-2"></i>Add Option
                                    </button>
                                </div>

                                <p class="text-sm text-gray-600 mb-4">
                                    Configure rush delivery options for invoices. Customers will be able to select from these options during checkout.
                                </p>

                                <div id="rush_options_container" class="space-y-4">
                                    @php
                                        $rushOptions = old('rush_options', $setting->rush_delivery_options ?? $setting->getDefaultRushOptions());
                                    @endphp

                                    @foreach ($rushOptions as $index => $option)
                                        <div class="rush-option-item bg-white p-4 border border-gray-300 rounded-lg" data-index="{{ $index }}">
                                            <div class="flex items-end gap-4">
                                                <div class="flex-1">
                                                    <label class="block text-gray-600 font-medium mb-2">Label</label>
                                                    <input type="text"
                                                           name="rush_options[{{ $index }}][label]"
                                                           value="{{ $option['label'] ?? '' }}"
                                                           placeholder="e.g., Express Delivery"
                                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                                           required
                                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                                </div>

                                                <div class="w-40">
                                                    <label class="block text-gray-600 font-medium mb-2">Days</label>
                                                    <input type="text"
                                                           name="rush_options[{{ $index }}][days]"
                                                           value="{{ $option['days'] ?? '' }}"
                                                           placeholder="2 or 'standard'"
                                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                                           required
                                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                                    <p class="text-xs text-gray-500 mt-1">Number or 'standard'</p>
                                                </div>

                                                <div class="w-32">
                                                    <label class="block text-gray-600 font-medium mb-2">Fee ($)</label>
                                                    <input type="number"
                                                           step="0.01"
                                                           name="rush_options[{{ $index }}][fee]"
                                                           value="{{ $option['fee'] ?? 0 }}"
                                                           placeholder="0.00"
                                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                                           required
                                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                                </div>

                                                @can('updateInvoice', $setting)
                                                    <button type="button"
                                                            class="remove-rush-option bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-all"
                                                            onclick="removeRushOption(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-700">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        <strong>Tip:</strong> For standard delivery (no rush fee), use 'standard' in the Days field and set Fee to 0.
                                    </p>
                                </div>
                            </div>

                            @can('updateInvoice', $setting)
                                <button type="submit"
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition-all mt-4">
                                    Save Invoice Settings
                                </button>
                            @endcan
                        </form>
                    </div>

                    {{-- 🌐 Webhook Settings --}}
                    @can('view', App\Models\WebhookSetting::class)
                        <div id="tab-content-webhooks" class="hidden">
                            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Webhook Settings</h2>

                            @cannot('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting)
                                <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                    <i class="fas fa-info-circle mr-2"></i> You have read-only access to these settings.
                                </div>
                            @endcannot

                            <form method="POST" action="{{ route('settings.webhook.update') }}" class="space-y-6">
                                @csrf

                                @php
                                    $userRole = auth()->user()->role ?? 'manager';
                                    $isAdminOrDeveloper = in_array($userRole, ['admin', 'developer']);

                                    // Mask Webhook URL
                                    $webhookSettingUrl = $webhookSetting->webhook_url ?? '';
                                    $maskedWebhookSettingUrl = $webhookSettingUrl
                                        ? str_repeat('*', max(0, strlen($webhookSettingUrl) - 4)) . substr($webhookSettingUrl, -4)
                                        : '';

                                    // Mask Webhook Secret
                                    $webhookSecret = $webhookSetting->webhook_secret ?? '';
                                    $maskedWebhookSecret = $webhookSecret
                                        ? str_repeat('*', max(0, strlen($webhookSecret) - 4)) . substr($webhookSecret, -4)
                                        : '';
                                @endphp

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook URL</label>
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="password"
                                            id="webhook_setting_url"
                                            name="webhook_url"
                                            value="{{ old('webhook_url', $maskedWebhookSettingUrl) }}"
                                            placeholder="https://example.com/webhook"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    id="toggleWebhookSettingUrl"
                                                    data-full-key="{{ $webhookSettingUrl }}"
                                                    class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook Secret</label>
                                    <div class="flex items-center space-x-3">
                                        <input
                                            type="password"
                                            id="webhook_secret"
                                            name="webhook_secret"
                                            value="{{ old('webhook_secret', $maskedWebhookSecret) }}"
                                            placeholder="secret-key"
                                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    id="toggleWebhookSecret"
                                                    data-full-key="{{ $webhookSecret }}"
                                                    class="bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>

                                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Customer Events</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach (['create', 'update', 'delete'] as $event)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-700 font-medium capitalize">Customer {{ $event }}</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="enable_customer_{{ $event }}" value="1"
                                                       class="sr-only peer"
                                                    {{ old("enable_customer_$event", $webhookSetting->{"enable_customer_$event"} ?? false) ? 'checked' : '' }}
                                                    {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>
                                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Product Events</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach (['create', 'update', 'delete'] as $event)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-700 font-medium capitalize">Product {{ $event }}</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="enable_product_{{ $event }}" value="1"
                                                       class="sr-only peer"
                                                    {{ old("enable_product_$event", $webhookSetting->{"enable_product_$event"} ?? false) ? 'checked' : '' }}
                                                    {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>
                                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Invoice Events</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @foreach (['create', 'update', 'delete'] as $event)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-700 font-medium capitalize">Invoice {{ $event }}</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="enable_invoice_{{ $event }}" value="1"
                                                       class="sr-only peer"
                                                    {{ old("enable_invoice_$event", $webhookSetting->{"enable_invoice_$event"} ?? false) ? 'checked' : '' }}
                                                    {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>
                                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                @can('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting)
                                    <button type="submit"
                                            class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition-all mt-6">
                                        Save Webhook Settings
                                    </button>
                                @endcan
                            </form>
                        </div>
                    @endcan

                    {{-- 🛡 Security Settings --}}
                    <div id="tab-content-security" class="hidden">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Security</h2>

                        <form method="POST" action="{{ route('settings.security.update') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Current Password</label>
                                <input type="password" name="current_password" placeholder="Enter current password"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">New Password</label>
                                <input type="password" name="new_password" placeholder="Enter new password"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" placeholder="Confirm new password"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition-all mt-4">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-7xl mx-auto">
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg">
                <i class="fas fa-exclamation-triangle mr-2"></i> You do not have permission to access settings.
            </div>
        </div>
    @endcan

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('logo-input')?.addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('logo-preview');

            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Integration Tab Keys
            setupKeyToggle('google_places_key', 'toggleGoogleKey');
            setupKeyToggle('stripe_public_key', 'toggleStripePublicKey');
            setupKeyToggle('stripe_secret_key', 'toggleStripeSecretKey');
            setupKeyToggle('webhook_url', 'toggleWebhookUrl');
            setupKeyToggle('integration_webhook_secret', 'toggleIntegrationWebhookSecret');

            // Invoice Configuration Tab Keys
            setupKeyToggle('tax_id_invoice', 'toggleTaxId');
            setupKeyToggle('starting_invoice_number', 'toggleInvoiceNumber');

            // Webhook Settings Tab Keys
            setupKeyToggle('webhook_setting_url', 'toggleWebhookSettingUrl');
            setupKeyToggle('webhook_secret', 'toggleWebhookSecret');
        });

        // Reusable function for key toggle functionality
        function setupKeyToggle(inputId, toggleId) {
            const input = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);

            if (input && toggle) {
                const fullKey = toggle.dataset.fullKey || '';

                toggle.addEventListener('click', () => {
                    const isMasked = input.type === 'password';

                    if (isMasked) {
                        // Show full key
                        input.type = 'text';
                        input.value = fullKey;
                        toggle.innerHTML = '<i class="fas fa-eye-slash"></i>';
                    } else {
                        // Mask key
                        const masked = fullKey
                            ? '*'.repeat(Math.max(0, fullKey.length - 4)) + fullKey.slice(-4)
                            : '';
                        input.type = 'password';
                        input.value = masked;
                        toggle.innerHTML = '<i class="fas fa-eye"></i>';
                    }
                });
            }
        }

        function copyWebhook(url) {
            navigator.clipboard.writeText(url).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Webhook URL Copied!',
                    text: 'The webhook URL has been copied to your clipboard.',
                    timer: 2000,
                    showConfirmButton: false,
                    position: 'bottom-end',
                    toast: true,
                    background: '#fff',
                    color: '#333',
                    customClass: {
                        popup: 'rounded-lg shadow-md'
                    }
                });
            });
        }

        const tabs = {
            org: document.getElementById('tab-org'),
            int: document.getElementById('tab-int'),
            invoice: document.getElementById('tab-invoice'),
            sec: document.getElementById('tab-security'),
            webhook: document.getElementById('tab-webhook'),
            contentOrg: document.getElementById('tab-content-org'),
            contentInt: document.getElementById('tab-content-int'),
            contentInvoice: document.getElementById('tab-content-invoice'),
            contentWebhook: document.getElementById('tab-content-webhooks'),
            contentSecurity: document.getElementById('tab-content-security'),
        };

        function switchTab(activeTab, activeContent) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-blue-600', 'bg-blue-50', 'border-blue-600');
                btn.classList.add('text-gray-600', 'border-transparent');
            });
            activeTab.classList.add('text-blue-600', 'bg-blue-50', 'border-blue-600');

            document.querySelectorAll('[id^="tab-content-"]').forEach(content => content.classList.add('hidden'));
            activeContent.classList.remove('hidden');
        }

        tabs.org?.addEventListener('click', () => switchTab(tabs.org, tabs.contentOrg));
        tabs.int?.addEventListener('click', () => switchTab(tabs.int, tabs.contentInt));
        tabs.invoice?.addEventListener('click', () => switchTab(tabs.invoice, tabs.contentInvoice));
        tabs.webhook?.addEventListener('click', () => switchTab(tabs.webhook, tabs.contentWebhook));
        tabs.sec?.addEventListener('click', () => switchTab(tabs.sec, tabs.contentSecurity));

        // Rush Delivery Toggle
        document.getElementById('enable_rush_delivery_toggle')?.addEventListener('change', function() {
            const rushSection = document.getElementById('rush_delivery_section');
            if (this.checked) {
                rushSection.classList.remove('hidden');
            } else {
                rushSection.classList.add('hidden');
            }
        });

        // Add Rush Option
        let rushOptionIndex = {{ count($rushOptions ?? []) }};
        document.getElementById('add_rush_option')?.addEventListener('click', function() {
            const container = document.getElementById('rush_options_container');
            const newOption = `
            <div class="rush-option-item bg-white p-4 border border-gray-300 rounded-lg" data-index="${rushOptionIndex}">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-gray-600 font-medium mb-2">Label</label>
                        <input type="text"
                               name="rush_options[${rushOptionIndex}][label]"
                               placeholder="e.g., Express Delivery"
                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>

                    <div class="w-40">
                        <label class="block text-gray-600 font-medium mb-2">Days</label>
                        <input type="text"
                               name="rush_options[${rushOptionIndex}][days]"
                               placeholder="2 or 'standard'"
                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Number or 'standard'</p>
                    </div>

                    <div class="w-32">
                        <label class="block text-gray-600 font-medium mb-2">Fee ($)</label>
                        <input type="number"
                               step="0.01"
                               name="rush_options[${rushOptionIndex}][fee]"
                               placeholder="0.00"
                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                               required>
                    </div>

                    <button type="button"
                            class="remove-rush-option bg-red-600 text-white px-4 py-2.5 rounded-lg hover:bg-red-700 transition-all"
                            onclick="removeRushOption(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
            container.insertAdjacentHTML('beforeend', newOption);
            rushOptionIndex++;
        });

        // Remove Rush Option
        function removeRushOption(button) {
            const container = document.getElementById('rush_options_container');
            const items = container.querySelectorAll('.rush-option-item');

            if (items.length > 1) {
                button.closest('.rush-option-item').remove();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Remove',
                    text: 'At least one rush delivery option must be configured.',
                    confirmButtonColor: '#3B82F6'
                });
            }
        }
    </script>
@endsection
