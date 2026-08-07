@extends('layouts.auth.app')

@section('title', 'Settings - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    @can('view', $setting)
        <div class="max-w-7xl mx-auto">
            <!-- 🔹 Page Header -->
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
                    <p class="text-gray-500 mt-1">Manage your organization, integrations, and preferences.</p>
                </div>
                <a href="{{ route('settings.tutorial') }}"
                   class="bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-blue-700 transition shrink-0">
                    <i class="fas fa-book-open mr-2"></i> Configuration Guide
                </a>
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

                        <button id="tab-mail"
                                class="tab-btn flex items-center px-6 py-3 text-left text-gray-600 hover:bg-gray-100 hover:text-blue-600 border-l-4 border-transparent">
                            <i class="fas fa-envelope mr-3"></i> Mail Configuration
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

                                    // Get ACTUAL values (never mask in PHP for form submission)
                                    $stripePublicKey = $setting->stripe_public_key ?? '';
                                    $stripeSecretKey = $setting->stripe_secret_key ?? '';
                                    $webhookUrl = $webhookUrl ?? '';
                                    $webhookSecret = $setting->webhook_secret ?? '';
                                    $googleKey = $setting->google_places_key ?? '';

                                    // Create MASKED versions for DISPLAY only
                                    $maskedPublicKey = $stripePublicKey
                                        ? str_repeat('*', max(0, strlen($stripePublicKey) - 4)) . substr($stripePublicKey, -4)
                                        : '';
                                    $maskedSecretKey = $stripeSecretKey
                                        ? str_repeat('*', max(0, strlen($stripeSecretKey) - 4)) . substr($stripeSecretKey, -4)
                                        : '';
                                    $maskedWebhookUrl = $webhookUrl
                                        ? str_repeat('*', max(0, strlen($webhookUrl) - 4)) . substr($webhookUrl, -4)
                                        : '';
                                    $maskedWebhookSecret = $webhookSecret
                                        ? str_repeat('*', max(0, strlen($webhookSecret) - 4)) . substr($webhookSecret, -4)
                                        : '';
                                    $maskedGoogleKey = $googleKey
                                        ? str_repeat('*', max(0, strlen($googleKey) - 4)) . substr($googleKey, -4)
                                        : '';
                                @endphp

                                    <!-- Stripe Public Key -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Stripe Public Key</label>
                                    <div class="flex items-center space-x-3">
                                        <!-- Hidden field with ACTUAL value (this gets submitted) -->
                                        <input type="hidden"
                                               name="stripe_public_key"
                                               id="stripe_public_key_actual"
                                               value="{{ old('stripe_public_key', $stripePublicKey) }}">

                                        <!-- Display field with MASKED value (for UI only) -->
                                        <input type="password"
                                               id="stripe_public_key_display"
                                               value="{{ $maskedPublicKey }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="pk_live_xxxxx"
                                               data-actual-field="stripe_public_key_actual"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                    data-display-field="stripe_public_key_display"
                                                    data-actual-field="stripe_public_key_actual"
                                                    data-full-key="{{ $stripePublicKey }}"
                                                    data-masked-key="{{ $maskedPublicKey }}">
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
                                        <input type="hidden"
                                               name="stripe_secret_key"
                                               id="stripe_secret_key_actual"
                                               value="{{ old('stripe_secret_key', $stripeSecretKey) }}">

                                        <input type="password"
                                               id="stripe_secret_key_display"
                                               value="{{ $maskedSecretKey }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="sk_live_xxxxx"
                                               data-actual-field="stripe_secret_key_actual"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                    data-display-field="stripe_secret_key_display"
                                                    data-actual-field="stripe_secret_key_actual"
                                                    data-full-key="{{ $stripeSecretKey }}"
                                                    data-masked-key="{{ $maskedSecretKey }}">
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
                                        <input type="password"
                                               id="webhook_url_display"
                                               readonly
                                               value="{{ $maskedWebhookUrl }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-100 cursor-not-allowed">

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                    data-display-field="webhook_url_display"
                                                    data-full-key="{{ $webhookUrl }}"
                                                    data-masked-key="{{ $maskedWebhookUrl }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif

                                        <button type="button"
                                                onclick="copyWebhook('{{ $webhookUrl }}')"
                                                class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition">
                                            Copy
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">This endpoint is unique to your account and identifies it on incoming webhooks.</p>
                                </div>

                                <!-- Webhook Secret -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook Secret</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="hidden"
                                               name="webhook_secret"
                                               id="integration_webhook_secret_actual"
                                               value="{{ old('webhook_secret', $webhookSecret) }}">

                                        <input type="password"
                                               id="integration_webhook_secret_display"
                                               value="{{ $maskedWebhookSecret }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               data-actual-field="integration_webhook_secret_actual"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                    data-display-field="integration_webhook_secret_display"
                                                    data-actual-field="integration_webhook_secret_actual"
                                                    data-full-key="{{ $webhookSecret }}"
                                                    data-masked-key="{{ $maskedWebhookSecret }}">
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
                                        <input type="hidden"
                                               name="google_places_key"
                                               id="google_places_key_actual"
                                               value="{{ old('google_places_key', $googleKey) }}">

                                        <input type="password"
                                               id="google_places_key_display"
                                               value="{{ $maskedGoogleKey }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="Enter your Google Places API Key"
                                               data-actual-field="google_places_key_actual"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                    data-display-field="google_places_key_display"
                                                    data-actual-field="google_places_key_actual"
                                                    data-full-key="{{ $googleKey }}"
                                                    data-masked-key="{{ $maskedGoogleKey }}">
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

                    {{-- ✉️ Mail Configuration --}}
                    <div id="tab-content-mail" class="hidden">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Mail Configuration</h2>

                        @cannot('updateIntegration', $setting)
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                <i class="fas fa-info-circle mr-2"></i> You have read-only access to these settings.
                            </div>
                        @endcannot

                        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Configure how <strong>invoice emails</strong> are sent from your account. Choose your own
                            mail provider (Brevo, SendGrid, Microsoft 365, or a custom SMTP server) or leave it on
                            "Platform Default" to use the platform's built-in mailer.
                        </div>

                        <form method="POST" action="{{ route('settings.mail.update') }}" class="space-y-6">
                            @csrf

                            @php
                                $presets = \App\Services\MailConfigurationService::providerPresets();
                                $mailPassword = $setting->mail_password ?? '';
                                $maskedMailPassword = $mailPassword
                                    ? str_repeat('*', max(0, strlen($mailPassword) - 4)) . substr($mailPassword, -4)
                                    : '';
                                $mailFromAddress = $setting->mail_from_address ?? '';
                                $mailFromName = $setting->mail_from_name ?? '';
                                $isMicrosoftConnected = $setting->organization_id
                                    ? \App\Models\MicrosoftToken::where('organization_id', $setting->organization_id)->exists()
                                    : \App\Models\MicrosoftToken::exists();
                            @endphp

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Mail Provider</label>
                                <select name="mail_mailer" id="mail_provider_select"
                                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 bg-white"
                                    {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                    <option value="platform_default" {{ old('mail_mailer', $setting->mail_mailer ?? 'platform_default') === 'platform_default' ? 'selected' : '' }}>
                                        Platform Default
                                    </option>
                                    <option value="brevo" {{ old('mail_mailer', $setting->mail_mailer) === 'brevo' ? 'selected' : '' }}>
                                        Brevo
                                    </option>
                                    <option value="sendgrid" {{ old('mail_mailer', $setting->mail_mailer) === 'sendgrid' ? 'selected' : '' }}>
                                        SendGrid
                                    </option>
                                    <option value="microsoft" {{ old('mail_mailer', $setting->mail_mailer) === 'microsoft' ? 'selected' : '' }}>
                                        Microsoft 365 (OAuth2)
                                    </option>
                                    <option value="smtp" {{ old('mail_mailer', $setting->mail_mailer) === 'smtp' ? 'selected' : '' }}>
                                        Custom SMTP
                                    </option>
                                    <option value="log" {{ old('mail_mailer', $setting->mail_mailer) === 'log' ? 'selected' : '' }}>
                                        Log (for testing)
                                    </option>
                                </select>
                            </div>

                            {{-- SMTP-based fields: brevo / sendgrid / smtp --}}
                            <div id="smtp-fields" class="space-y-6 hidden">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-gray-600 font-medium mb-2">SMTP Host</label>
                                        <input type="text" name="mail_host" id="mail_host_input"
                                               value="{{ old('mail_host', $setting->mail_host ?? '') }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="smtp.example.com"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                    </div>

                                    <div>
                                        <label class="block text-gray-600 font-medium mb-2">Port</label>
                                        <input type="number" name="mail_port" id="mail_port_input"
                                               value="{{ old('mail_port', $setting->mail_port ?? '') }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="587"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-gray-600 font-medium mb-2">Username</label>
                                        <input type="text" name="mail_username" id="mail_username_input"
                                               value="{{ old('mail_username', $setting->mail_username ?? '') }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="SMTP username"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                        <p id="mail_username_hint" class="text-xs text-gray-500 mt-1"></p>
                                    </div>

                                    <div>
                                        <label class="block text-gray-600 font-medium mb-2">Password / API Key</label>
                                        <div class="flex items-center space-x-3">
                                            <input type="hidden" name="mail_password" id="mail_password_actual"
                                                   value="{{ old('mail_password', $mailPassword) }}">
                                            <input type="password" id="mail_password_display"
                                                   value="{{ $maskedMailPassword }}"
                                                   class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                                   placeholder="••••••••"
                                                   data-actual-field="mail_password_actual"
                                                {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                            @if($isAdminOrDeveloper)
                                                <button type="button"
                                                        class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                        data-display-field="mail_password_display"
                                                        data-actual-field="mail_password_actual"
                                                        data-full-key="{{ $mailPassword }}"
                                                        data-masked-key="{{ $maskedMailPassword }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Encryption</label>
                                    <select name="mail_encryption" class="w-full border border-gray-300 rounded-lg p-2.5 bg-white"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                        <option value="tls" {{ old('mail_encryption', $setting->mail_encryption ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ old('mail_encryption', $setting->mail_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ old('mail_encryption', $setting->mail_encryption) === 'none' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Microsoft section --}}
                            <div id="microsoft-fields" class="hidden">
                                <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <label class="block text-gray-600 font-medium mb-2">Microsoft 365 Sender</label>
                                    <input type="text" name="mail_username"
                                           value="{{ old('mail_username', $setting->mail_username ?? '') }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                           placeholder="you@yourcompany.com"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                    <p class="text-xs text-gray-500 mt-1">
                                        The mailbox address used to authenticate with Microsoft (OAuth2). Connect the account below.
                                    </p>
                                </div>

                                <div class="mb-4 flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg">
                                    <div>
                                        <p class="text-gray-700 font-medium">
                                            <i class="fas fa-microsoft mr-1 text-blue-600"></i> Microsoft OAuth2 Connection
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            @if($isMicrosoftConnected)
                                                <span class="text-green-600 font-medium">Connected</span> — your Microsoft 365 token is stored for this account.
                                            @else
                                                <span class="text-red-500 font-medium">Not connected</span> — connect to enable sending via Microsoft 365.
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ route('auth.redirect') }}"
                                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition shrink-0">
                                        <i class="fas fa-link mr-1"></i> Connect Microsoft 365
                                    </a>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">From Address</label>
                                    <input type="email" name="mail_from_address"
                                           value="{{ old('mail_from_address', $mailFromAddress) }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                           placeholder="invoices@yourcompany.com"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">From Name</label>
                                    <input type="text" name="mail_from_name"
                                           value="{{ old('mail_from_name', $mailFromName) }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                           placeholder="Your Company"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                </div>
                            </div>

                            @can('updateIntegration', $setting)
                                <button type="submit"
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition-all">
                                    Save Mail Configuration
                                </button>
                            @endcan
                        </form>

                        @can('updateIntegration', $setting)
                            <form method="POST" action="{{ route('settings.mail.test') }}" class="mt-8 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                                @csrf
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">Send Test Email</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    Verify your configuration by sending a test message to an address you control.
                                </p>
                                <div class="flex items-end gap-4">
                                    <div class="flex-1">
                                        <label class="block text-gray-600 font-medium mb-2">Recipient</label>
                                        <input type="email" name="test_email"
                                               value="{{ old('test_email', auth()->user()->email ?? '') }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="you@example.com">
                                    </div>
                                    <button type="submit"
                                            class="bg-green-600 text-white px-6 py-2.5 rounded-lg shadow hover:bg-green-700 transition-all">
                                        <i class="fas fa-paper-plane mr-2"></i>Send Test Email
                                    </button>
                                </div>
                            </form>
                        @endcan
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

                                // Get ACTUAL values
                                $taxId = $setting->tax_id ?? '';
                                $invoiceNumber = $setting->starting_invoice_number ?? 'INV-' . date('Y') . '-001';

                                // Create MASKED versions for display
                                $maskedTaxId = $taxId
                                    ? str_repeat('*', max(0, strlen($taxId) - 4)) . substr($taxId, -4)
                                    : '';
                                $maskedInvoiceNumber = $invoiceNumber
                                    ? str_repeat('*', max(0, strlen($invoiceNumber) - 4)) . substr($invoiceNumber, -4)
                                    : '';
                            @endphp

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Tax ID</label>
                                <div class="flex items-center space-x-3">
                                    <input type="hidden"
                                           name="tax_id_invoice"
                                           id="tax_id_invoice_actual"
                                           value="{{ old('tax_id_invoice', $taxId) }}">

                                    <input type="password"
                                           id="tax_id_invoice_display"
                                           value="{{ $maskedTaxId }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="123-456-789"
                                           data-actual-field="tax_id_invoice_actual"
                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>

                                    @if($isAdminOrDeveloper)
                                        <button type="button"
                                                class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                data-display-field="tax_id_invoice_display"
                                                data-actual-field="tax_id_invoice_actual"
                                                data-full-key="{{ $taxId }}"
                                                data-masked-key="{{ $maskedTaxId }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Starting Invoice Number</label>
                                <div class="flex items-center space-x-3">
                                    <input type="hidden"
                                           name="starting_invoice_number"
                                           id="starting_invoice_number_actual"
                                           value="{{ old('starting_invoice_number', $invoiceNumber) }}">

                                    <input type="password"
                                           id="starting_invoice_number_display"
                                           value="{{ $maskedInvoiceNumber }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="INV-2025-001"
                                           data-actual-field="starting_invoice_number_actual"
                                        {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>

                                    @if($isAdminOrDeveloper)
                                        <button type="button"
                                                class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                data-display-field="starting_invoice_number_display"
                                                data-actual-field="starting_invoice_number_actual"
                                                data-full-key="{{ $invoiceNumber }}"
                                                data-masked-key="{{ $maskedInvoiceNumber }}">
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

                            @php
                                $accountWebhookUrl = !empty($organization->webhook_identifier)
                                    ? secure_url('/webhook/' . $organization->webhook_identifier)
                                    : secure_url('/webhook');
                                $accountWebhookIdentifier = $organization->webhook_identifier ?? '';
                            @endphp

                            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <label class="block text-gray-700 font-medium mb-2">
                                    <i class="fas fa-fingerprint mr-1 text-blue-600"></i> Your Unique Webhook Endpoint
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="text"
                                           value="{{ $accountWebhookUrl }}"
                                           readonly
                                           class="w-full border border-gray-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-blue-500 font-mono text-sm">
                                    <button type="button"
                                            onclick="copyWebhook('{{ $accountWebhookUrl }}')"
                                            class="bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition shrink-0">
                                        Copy
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    This endpoint is unique to your account. Send events to
                                    <span class="font-mono">/webhook/{{ $accountWebhookIdentifier }}</span>
                                    and we will identify and handle them for your account, just like the default webhook.
                                </p>
                            </div>

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

                                    // Get ACTUAL values
                                    $webhookSettingUrl = $webhookSetting->webhook_url ?? '';
                                    $webhookSettingSecret = $webhookSetting->webhook_secret ?? '';

                                    // Create MASKED versions
                                    $maskedWebhookSettingUrl = $webhookSettingUrl
                                        ? str_repeat('*', max(0, strlen($webhookSettingUrl) - 4)) . substr($webhookSettingUrl, -4)
                                        : '';
                                    $maskedWebhookSettingSecret = $webhookSettingSecret
                                        ? str_repeat('*', max(0, strlen($webhookSettingSecret) - 4)) . substr($webhookSettingSecret, -4)
                                        : '';
                                @endphp

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook URL</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="hidden"
                                               name="webhook_url"
                                               id="webhook_setting_url_actual"
                                               value="{{ old('webhook_url', $webhookSettingUrl) }}">

                                        <input type="password"
                                               id="webhook_setting_url_display"
                                               value="{{ $maskedWebhookSettingUrl }}"
                                               placeholder="https://example.com/webhook"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               data-actual-field="webhook_setting_url_actual"
                                            {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                    data-display-field="webhook_setting_url_display"
                                                    data-actual-field="webhook_setting_url_actual"
                                                    data-full-key="{{ $webhookSettingUrl }}"
                                                    data-masked-key="{{ $maskedWebhookSettingUrl }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Only the last 4 characters are shown by default.</p>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook Secret</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="hidden"
                                               name="webhook_secret"
                                               id="webhook_setting_secret_actual"
                                               value="{{ old('webhook_secret', $webhookSettingSecret) }}">

                                        <input type="password"
                                               id="webhook_setting_secret_display"
                                               value="{{ $maskedWebhookSettingSecret }}"
                                               placeholder="secret-key"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               data-actual-field="webhook_setting_secret_actual"
                                            {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>

                                        @if($isAdminOrDeveloper)
                                            <button type="button"
                                                    class="toggle-key-btn bg-gray-100 border border-gray-300 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-200 transition"
                                                    data-display-field="webhook_setting_secret_display"
                                                    data-actual-field="webhook_setting_secret_actual"
                                                    data-full-key="{{ $webhookSettingSecret }}"
                                                    data-masked-key="{{ $maskedWebhookSettingSecret }}">
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
            // Universal toggle key visibility handler
            document.querySelectorAll('.toggle-key-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const displayField = document.getElementById(this.dataset.displayField);
                    const fullKey = this.dataset.fullKey || '';
                    const maskedKey = this.dataset.maskedKey || '';
                    const icon = this.querySelector('i');

                    if (displayField.type === 'password') {
                        // Show full key
                        displayField.type = 'text';
                        displayField.value = fullKey;
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        // Hide key (show masked)
                        displayField.type = 'password';
                        displayField.value = maskedKey;
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });

            // Handle user input in display fields - update the hidden actual field
            document.querySelectorAll('input[data-actual-field]').forEach(displayField => {
                displayField.addEventListener('input', function() {
                    const actualFieldId = this.dataset.actualField;
                    const actualField = document.getElementById(actualFieldId);

                    if (actualField) {
                        // User is typing a new value - update the hidden field
                        actualField.value = this.value;
                    }
                });

                // When field gains focus and user starts typing, clear the masked placeholder
                displayField.addEventListener('focus', function() {
                    if (this.type === 'password' && this.value.includes('*')) {
                        // Clear masked value so user can type new value
                        this.value = '';
                    }
                });

                // When field loses focus, if it's empty, restore the masked value
                displayField.addEventListener('blur', function() {
                    const actualFieldId = this.dataset.actualField;
                    const actualField = document.getElementById(actualFieldId);

                    if (this.value === '' && actualField && actualField.value) {
                        // Restore masked version if user didn't enter anything
                        const fullValue = actualField.value;
                        const masked = '*'.repeat(Math.max(0, fullValue.length - 4)) + fullValue.slice(-4);
                        this.value = masked;
                    }
                });
            });
        });

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
            mail: document.getElementById('tab-mail'),
            invoice: document.getElementById('tab-invoice'),
            sec: document.getElementById('tab-security'),
            webhook: document.getElementById('tab-webhook'),
            contentOrg: document.getElementById('tab-content-org'),
            contentInt: document.getElementById('tab-content-int'),
            contentMail: document.getElementById('tab-content-mail'),
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
        tabs.mail?.addEventListener('click', () => switchTab(tabs.mail, tabs.contentMail));
        tabs.invoice?.addEventListener('click', () => switchTab(tabs.invoice, tabs.contentInvoice));
        tabs.webhook?.addEventListener('click', () => switchTab(tabs.webhook, tabs.contentWebhook));
        tabs.sec?.addEventListener('click', () => switchTab(tabs.sec, tabs.contentSecurity));

        // Mail provider field toggling + presets
        const mailPresets = @json(\App\Services\MailConfigurationService::providerPresets());
        const mailProviderSelect = document.getElementById('mail_provider_select');

        function applyMailProviderFields() {
            const provider = mailProviderSelect?.value || 'platform_default';
            const smtpFields = document.getElementById('smtp-fields');
            const msFields = document.getElementById('microsoft-fields');

            const isSmtp = ['brevo', 'sendgrid', 'smtp'].includes(provider);
            const isMs = provider === 'microsoft';

            smtpFields?.classList.toggle('hidden', !isSmtp);
            msFields?.classList.toggle('hidden', !isMs);

            // Prefill preset defaults for hosts/ports (only when a preset exists)
            if (mailPresets && mailPresets[provider]) {
                const preset = mailPresets[provider];
                const hostInput = document.getElementById('mail_host_input');
                const portInput = document.getElementById('mail_port_input');
                const usernameHint = document.getElementById('mail_username_hint');

                if (hostInput && !hostInput.value) {
                    hostInput.value = preset.host || '';
                }
                if (portInput && !portInput.value) {
                    portInput.value = preset.port || '';
                }
                if (usernameHint) {
                    usernameHint.textContent = preset.username_hint || '';
                }
            }
        }

        mailProviderSelect?.addEventListener('change', applyMailProviderFields);
        applyMailProviderFields();

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
