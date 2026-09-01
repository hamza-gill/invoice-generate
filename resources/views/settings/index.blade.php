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

                        <button id="tab-reminder"
                                class="tab-btn flex items-center px-6 py-3 text-left text-gray-600 hover:bg-gray-100 hover:text-blue-600 border-l-4 border-transparent">
                            <i class="fas fa-bell mr-3"></i> Reminders
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
                                <!-- Stripe Public Key -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Stripe Public Key</label>
                                    <input type="text"
                                           name="stripe_public_key"
                                           value="{{ old('stripe_public_key', $setting->stripe_public_key ?? '') }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                           placeholder="pk_live_xxxxx"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                </div>

                                <!-- Stripe Secret Key -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Stripe Secret Key</label>
                                    <input type="text"
                                           name="stripe_secret_key"
                                           value="{{ old('stripe_secret_key', $setting->stripe_secret_key ?? '') }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                           placeholder="sk_live_xxxxx"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                </div>

                                <!-- Webhook URL -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook URL</label>
                                    <div class="flex items-center space-x-3">
                                        <input type="text"
                                               name="webhook_url"
                                               readonly
                                               value="{{ $webhookUrl }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 bg-gray-100 cursor-not-allowed">
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
                                    <input type="text"
                                           name="webhook_secret"
                                           value="{{ old('webhook_secret', $setting->webhook_secret ?? '') }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                           placeholder="whsec_xxxxx"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
                                </div>

                                <!-- Google Places API Key -->
                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Google Places API Key</label>
                                    <input type="text"
                                           name="google_places_key"
                                           value="{{ old('google_places_key', $setting->google_places_key ?? '') }}"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                           placeholder="Enter your Google Places API Key"
                                        {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
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
                                $userRole = auth()->user()->role ?? 'manager';
                                $isAdminOrDeveloper = in_array($userRole, ['admin', 'developer']);
                                $mailPassword = $setting->mail_password ?? '';
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
                                        <input type="text" name="mail_password"
                                               value="{{ old('mail_password', $mailPassword) }}"
                                               class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                               placeholder="SMTP password or API key"
                                            {{ !Gate::allows('updateIntegration', $setting) ? 'disabled' : '' }}>
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
                                $taxId = $setting->tax_id ?? '';
                                $invoiceNumber = $setting->starting_invoice_number ?? 'INV-' . date('Y') . '-001';
                            @endphp

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Tax ID</label>
                                <input type="text"
                                       name="tax_id_invoice"
                                       value="{{ old('tax_id_invoice', $taxId) }}"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="123-456-789"
                                    {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                            </div>

                            <div>
                                <label class="block text-gray-600 font-medium mb-2">Starting Invoice Number</label>
                                <input type="text"
                                       name="starting_invoice_number"
                                       value="{{ old('starting_invoice_number', $invoiceNumber) }}"
                                       class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="INV-2025-001"
                                    {{ !Gate::allows('updateInvoice', $setting) ? 'disabled' : '' }}>
                                <p class="text-sm text-gray-500 mt-1">
                                    Set the starting invoice number. The next invoices will auto-increment from this number.
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
                                    $webhookSettingUrl = $webhookSetting->webhook_url ?? '';
                                    $webhookSettingSecret = $webhookSetting->webhook_secret ?? '';
                                @endphp

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook URL</label>
                                    <input type="text"
                                           name="webhook_url"
                                           value="{{ old('webhook_url', $webhookSettingUrl) }}"
                                           placeholder="https://example.com/webhook"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>
                                </div>

                                <div>
                                    <label class="block text-gray-600 font-medium mb-2">Webhook Secret</label>
                                    <input type="text"
                                           name="webhook_secret"
                                           value="{{ old('webhook_secret', $webhookSettingSecret) }}"
                                           placeholder="secret-key"
                                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        {{ !Gate::allows('updateWebhook', $webhookSetting ?? new App\Models\WebhookSetting) ? 'disabled' : '' }}>
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

                    {{-- 🔔 Reminder Settings --}}
                    <div id="tab-content-reminder" class="hidden">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Invoice Payment Reminders</h2>

                        @cannot('updateReminder', $setting)
                            <div class="mb-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
                                <i class="fas fa-info-circle mr-2"></i> You have read-only access to these settings.
                            </div>
                        @endcan

                        <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Automatically email your customers when an invoice becomes overdue. Set the number of days
                            after the due date at which an escalating reminder should be sent (e.g. 1, 7, 14 days).
                            Each reminder is sent only once per invoice.
                        </div>

                        <form method="POST" action="{{ route('settings.reminder.update') }}" class="space-y-6" id="reminder-form">
                            @csrf

                            @php
                                $reminderEnabled = (bool) ($setting->enable_invoice_reminders ?? false);
                                $reminderSteps = method_exists($setting, 'reminderSteps') ? $setting->reminderSteps() : [1, 7, 14];
                            @endphp

                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Enable Automatic Reminders</h3>
                                    <p class="text-sm text-gray-500">Send overdue invoice reminders to your customers.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_invoice_reminders" id="enable_reminders_input" value="1"
                                           class="sr-only peer"
                                        {{ $reminderEnabled ? 'checked' : '' }}
                                        {{ !Gate::allows('updateReminder', $setting) ? 'disabled' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-blue-600 transition-all"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow-md peer-checked:translate-x-5 transition-transform"></div>
                                </label>
                            </div>

                            <div id="reminder-steps-wrapper" class="{{ $reminderEnabled ? '' : 'opacity-40 pointer-events-none' }}">
                                <h3 class="text-lg font-semibold text-gray-800 mb-3">Escalation Steps (days after due date)</h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    Each row is a reminder sent that many days after the invoice becomes overdue.
                                    The customer is only emailed once per step.
                                </p>

                                <div id="reminder-steps" class="space-y-3">
                                    @foreach ($reminderSteps as $index => $days)
                                        <div class="flex items-center gap-3 reminder-step-row">
                                            <input type="number" name="reminder_days[]" min="1" max="365"
                                                   value="{{ $days }}"
                                                   class="w-40 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500"
                                                {{ !Gate::allows('updateReminder', $setting) ? 'disabled' : '' }}>
                                            <span class="text-sm text-gray-500">day(s) after due date</span>
                                            <button type="button" class="text-red-500 hover:text-red-700 remove-reminder-step" title="Remove step" {{ !Gate::allows('updateReminder', $setting) ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <button type="button" id="add-reminder-step"
                                        class="mt-3 text-blue-600 hover:text-blue-800 text-sm font-medium"
                                    {{ !Gate::allows('updateReminder', $setting) ? 'disabled' : '' }}>
                                    <i class="fas fa-plus mr-1"></i> Add escalation step
                                </button>
                            </div>

                            @can('updateReminder', $setting)
                                <button type="submit"
                                        class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition-all">
                                    Save Reminder Settings
                                </button>
                            @endcan
                        </form>
                    </div>

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
            reminder: document.getElementById('tab-reminder'),
            sec: document.getElementById('tab-security'),
            webhook: document.getElementById('tab-webhook'),
            contentOrg: document.getElementById('tab-content-org'),
            contentInt: document.getElementById('tab-content-int'),
            contentMail: document.getElementById('tab-content-mail'),
            contentInvoice: document.getElementById('tab-content-invoice'),
            contentReminder: document.getElementById('tab-content-reminder'),
            contentWebhook: document.getElementById('tab-content-webhooks'),
            contentSecurity: document.getElementById('tab-content-security'),
        };

        function switchTab(activeTab, activeContent, tabKey) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('text-blue-600', 'bg-blue-50', 'border-blue-600');
                btn.classList.add('text-gray-600', 'border-transparent');
            });
            activeTab.classList.add('text-blue-600', 'bg-blue-50', 'border-blue-600');

            document.querySelectorAll('[id^="tab-content-"]').forEach(content => content.classList.add('hidden'));
            activeContent.classList.remove('hidden');

            if (tabKey) {
                localStorage.setItem('inveqi_active_tab', tabKey);
            }
        }

        const tabMap = [
            { key: 'org', btn: tabs.org, content: tabs.contentOrg },
            { key: 'int', btn: tabs.int, content: tabs.contentInt },
            { key: 'mail', btn: tabs.mail, content: tabs.contentMail },
            { key: 'invoice', btn: tabs.invoice, content: tabs.contentInvoice },
            { key: 'reminder', btn: tabs.reminder, content: tabs.contentReminder },
            { key: 'webhook', btn: tabs.webhook, content: tabs.contentWebhook },
            { key: 'security', btn: tabs.sec, content: tabs.contentSecurity },
        ];

        tabMap.forEach(tab => {
            tab.btn?.addEventListener('click', () => switchTab(tab.btn, tab.content, tab.key));
        });

        const savedTab = localStorage.getItem('inveqi_active_tab') || 'org';
        const active = tabMap.find(t => t.key === savedTab && t.btn && t.content);
        if (active) {
            switchTab(active.btn, active.content);
        }

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

        // Reminder settings: toggle steps and add/remove rows
        const reminderToggle = document.getElementById('enable_reminders_input');
        const reminderStepsWrapper = document.getElementById('reminder-steps-wrapper');

        function syncReminderSteps() {
            if (reminderStepsWrapper) {
                const enabled = reminderToggle?.checked ?? false;
                reminderStepsWrapper.classList.toggle('opacity-40', !enabled);
                reminderStepsWrapper.classList.toggle('pointer-events-none', !enabled);
            }
        }

        reminderToggle?.addEventListener('change', syncReminderSteps);

        document.getElementById('add-reminder-step')?.addEventListener('click', function () {
            const container = document.getElementById('reminder-steps');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-3 reminder-step-row';
            row.innerHTML =
                '<input type="number" name="reminder_days[]" min="1" max="365" value="7" ' +
                'class="w-40 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500">' +
                '<span class="text-sm text-gray-500">day(s) after due date</span>' +
                '<button type="button" class="text-red-500 hover:text-red-700 remove-reminder-step" title="Remove step"><i class="fas fa-trash"></i></button>';
            container.appendChild(row);
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.remove-reminder-step')) {
                e.target.closest('.reminder-step-row')?.remove();
            }
        });

        syncReminderSteps();
    </script>
@endsection
