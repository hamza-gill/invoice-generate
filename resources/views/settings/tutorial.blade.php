@extends('layouts.auth.app')

@section('title', 'Configuration Guide - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Header --}}
        <div class="mb-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Configuration Guide</h1>
                    <p class="text-gray-500 mt-2">
                        Step-by-step tutorials for every setting so you can configure
                        <span class="font-semibold text-blue-600">{{ $globalSettings->company_name ?? config('app.name') }}</span>
                        correctly and get started without delay.
                    </p>
                </div>
                <a href="{{ route('settings.index') }}"
                   class="bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow hover:bg-blue-700 transition shrink-0">
                    <i class="fas fa-cog mr-2"></i> Open Settings
                </a>
            </div>
        </div>

        {{-- Section index --}}
        <div class="bg-white shadow-lg rounded-2xl p-6 mb-10">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Jump to a tutorial</h2>
            <div class="grid md:grid-cols-3 gap-3">
                <a href="#tutorial-org" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                    <i class="fas fa-building mr-3 text-blue-600"></i> Organization Settings
                </a>
                <a href="#tutorial-int" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                    <i class="fas fa-plug mr-3 text-blue-600"></i> Integrations
                </a>
                <a href="#tutorial-mail" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                    <i class="fas fa-envelope mr-3 text-blue-600"></i> Mail Configuration
                </a>
                <a href="#tutorial-invoice" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                    <i class="fas fa-file-invoice-dollar mr-3 text-blue-600"></i> Invoice Configuration
                </a>
                <a href="#tutorial-webhook" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                    <i class="fas fa-link mr-3 text-blue-600"></i> Webhook Settings
                </a>
                <a href="#tutorial-security" class="flex items-center p-3 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                    <i class="fas fa-shield-alt mr-3 text-blue-600"></i> Security
                </a>
            </div>
        </div>

        {{-- Tutorial template helper (kept local per section) --}}
        @php
            $steps = function (array $items) {
                $html = '<ol class="space-y-3">';
                foreach ($items as $i => $item) {
                    $html .= '<li class="flex gap-3"><span class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-medium shrink-0">' . ($i + 1) . '</span><span class="text-gray-600 text-sm leading-relaxed">' . $item . '</span></li>';
                }
                return $html . '</ol>';
            };
        @endphp

        {{-- 1. Organization Settings --}}
        <section id="tutorial-org" class="bg-white shadow-lg rounded-2xl p-8 mb-8 scroll-mt-6">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-4"><i class="fas fa-building text-xl"></i></div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">1. Organization Settings</h2>
                    <p class="text-gray-500 text-sm">Sidebar: <strong>Settings → Organization</strong></p>
                </div>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                This section stores your company identity and the details that appear on your invoices.
                Only <strong>Admin</strong> and <strong>Developer</strong> roles can edit these fields; managers have read-only access.
            </p>

            {!! $steps([
                '<strong>Company Name</strong> — enter the official business name. It appears on invoices, emails, and receipts.',
                '<strong>Company Email</strong> — the public contact email shown to your customers.',
                '<strong>Country</strong> — select your country; used for tax and address formatting.',
                '<strong>Base Currency</strong> — choose the currency used on all invoices (USD, EUR, GBP, etc.). Changing this affects new invoices.',
                '<strong>Tax Percentage</strong> — the default VAT/sales tax rate applied to invoice totals. Leave blank if you do not charge tax.',
                '<strong>Company Logo</strong> — upload your logo (PNG/JPG). It appears in the invoice header.',
                '<strong>Address</strong> — your business address printed at the top of invoices.',
                '<strong>Invoice Notes</strong> — a short message shown on invoices (e.g. payment instructions).',
                '<strong>Terms &amp; Conditions</strong> — your payment and service terms printed at the bottom of invoices.',
                'Click <strong>Save Changes</strong> at the bottom when finished.',
            ]) !!}
        </section>

        {{-- 2. Integrations --}}
        <section id="tutorial-int" class="bg-white shadow-lg rounded-2xl p-8 mb-8 scroll-mt-6">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-4"><i class="fas fa-plug text-xl"></i></div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">2. Integrations</h2>
                    <p class="text-gray-500 text-sm">Sidebar: <strong>Settings → Integrations</strong></p>
                </div>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                Connect external services (Stripe and Google Places) to your account. Keys are shown masked;
                only the last 4 characters are visible. Use the <i class="fas fa-eye"></i> button to reveal a key.
            </p>

            {!! $steps([
                '<strong>Stripe Public Key</strong> — your Stripe <code>pk_live_...</code> or <code>pk_test_...</code> publishable key from the Stripe Dashboard → Developers → API keys.',
                '<strong>Stripe Secret Key</strong> — your Stripe <code>sk_live_...</code> or <code>sk_test_...</code> secret key. Keep this private; never share it.',
                '<strong>Webhook URL</strong> — your unique, read-only webhook endpoint (see the Webhook Settings tutorial below). Copy it and register it in Stripe so payment events reach your account.',
                '<strong>Webhook Secret</strong> — the <code>whsec_...</code> signing secret from Stripe used to verify incoming webhooks.',
                '<strong>Google Places API Key</strong> — optional; enables address autocomplete in customer forms. Generate it from the Google Cloud Console.',
                'Click <strong>Save Integration</strong> to apply the keys.',
            ]) !!}

            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-700"><i class="fas fa-exclamation-triangle mr-2"></i>
                    To accept online payments, your subscription plan must include the payment gateway and
                    <strong>Payment Gateway</strong> must be enabled. Stripe webhooks must be configured on the
                    Stripe side using the Webhook URL and Secret from this page.
                </p>
            </div>
        </section>

        {{-- 3. Mail Configuration --}}
        <section id="tutorial-mail" class="bg-white shadow-lg rounded-2xl p-8 mb-8 scroll-mt-6">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 text-purple-600 p-3 rounded-lg mr-4"><i class="fas fa-envelope text-xl"></i></div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">3. Mail Configuration</h2>
                    <p class="text-gray-500 text-sm">Sidebar: <strong>Settings → Mail Configuration</strong></p>
                </div>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                Control how <strong>invoice emails</strong> are sent from your account. You can use your own mail
                provider so emails come from your own sending account, or keep the platform's built-in mailer.
            </p>

            <h3 class="font-semibold text-gray-800 mb-3">Choosing a provider</h3>
            {!! $steps([
                'Open <strong>Mail Configuration</strong> and pick a provider from the dropdown.',
                '<strong>Platform Default</strong> — use the platform mailer. No extra setup required.',
                '<strong>Brevo</strong> — enter the SMTP login and SMTP key from your Brevo account (Settings → SMTP &amp; API). Host and port are pre-filled (<code>smtp-relay.brevo.com:587</code>).',
                '<strong>SendGrid</strong> — set Username to the literal value <code>apikey</code> and Password to your SendGrid API key (Settings → API Keys). Host <code>smtp.sendgrid.net:587</code> is pre-filled.',
                '<strong>Microsoft 365</strong> — type the mailbox address you want to send from, then click <strong>Connect Microsoft 365</strong> and sign in. The app stores a per-account OAuth2 token.',
                '<strong>Custom SMTP</strong> — enter your own host, port, username, password, and encryption (TLS/SSL/None).',
                '<strong>Log</strong> — writes emails to the log instead of sending. Useful for testing.',
                'Set the <strong>From Address</strong> and <strong>From Name</strong> customers will see as the sender.',
                'Click <strong>Save Mail Configuration</strong>, then use <strong>Send Test Email</strong> to verify everything works.',
            ]) !!}

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-700"><i class="fas fa-info-circle mr-2"></i>
                    <strong>Tip:</strong> the From Address should be a sender you have verified with your provider
                    (e.g. an approved sender/domain in Brevo, a verified sender in SendGrid, or a licensed mailbox in
                    Microsoft 365). Otherwise providers may reject the email.
                </p>
            </div>
        </section>

        {{-- 4. Invoice Configuration --}}
        <section id="tutorial-invoice" class="bg-white shadow-lg rounded-2xl p-8 mb-8 scroll-mt-6">
            <div class="flex items-center mb-4">
                <div class="bg-indigo-100 text-indigo-600 p-3 rounded-lg mr-4"><i class="fas fa-file-invoice-dollar text-xl"></i></div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">4. Invoice Configuration</h2>
                    <p class="text-gray-500 text-sm">Sidebar: <strong>Settings → Invoice Configuration</strong></p>
                </div>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                Control invoice numbering, taxes, and optional customer-facing features.
            </p>

            {!! $steps([
                '<strong>Tax ID</strong> — your tax/VAT registration number printed on invoices. Masked by default; use the eye button to reveal.',
                '<strong>Starting Invoice Number</strong> — set the first invoice number (format <code>INV-YYYY-NNN</code>). New invoices increment automatically from here.',
                '<strong>Enable Terms &amp; Conditions</strong> — show the Terms &amp; Conditions text on invoices.',
                '<strong>Enable Invoice Notes</strong> — show the Notes field on invoices.',
                '<strong>Enable Due Date</strong> — show a payment due date and require it when creating invoices.',
                '<strong>Enable Tax</strong> — apply the tax percentage to invoice totals.',
                '<strong>Enable Tax ID</strong> — show the Tax ID field on invoices.',
                '<strong>Enable Rush Delivery</strong> — let customers pick a rush delivery option during checkout. Add/remove options with the green <strong>Add Option</strong> button (set Days to <code>standard</code> and Fee to <code>0</code> for free standard delivery).',
                'Click <strong>Save Invoice Settings</strong> when done.',
            ]) !!}
        </section>

        {{-- 5. Webhook Settings --}}
        <section id="tutorial-webhook" class="bg-white shadow-lg rounded-2xl p-8 mb-8 scroll-mt-6">
            <div class="flex items-center mb-4">
                <div class="bg-teal-100 text-teal-600 p-3 rounded-lg mr-4"><i class="fas fa-link text-xl"></i></div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">5. Webhook Settings</h2>
                    <p class="text-gray-500 text-sm">Sidebar: <strong>Settings → Webhook Settings</strong></p>
                </div>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                Webhooks let external systems receive real-time notifications when your customers, products, or
                invoices change. Every account gets a <strong>unique webhook endpoint</strong> that identifies it.
            </p>

            {!! $steps([
                'Copy your <strong>Unique Webhook Endpoint</strong> from the top of the page. Each account has its own URL — do not share it between accounts.',
                '<strong>Webhook URL</strong> — where the recipient server should be called. This is optional unless you are forwarding events to another system.',
                '<strong>Webhook Secret</strong> — a shared secret used to sign webhook payloads so the receiver can verify them.',
                'Enable the events you want to send: <strong>Customer create/update/delete</strong>, <strong>Product create/update/delete</strong>, and <strong>Invoice create/update/delete</strong>.',
                'Click <strong>Save Webhook Settings</strong>. Events will be delivered to your configured URL (or handled internally by the platform).',
            ]) !!}

            <div class="mt-6 p-4 bg-teal-50 border border-teal-200 rounded-lg">
                <p class="text-sm text-teal-700"><i class="fas fa-info-circle mr-2"></i>
                    If you are connecting Stripe, note that the Stripe webhook endpoint lives in the
                    <strong>Integrations</strong> tab, while this tab manages event notifications to your own systems.
                </p>
            </div>
        </section>

        {{-- 6. Security --}}
        <section id="tutorial-security" class="bg-white shadow-lg rounded-2xl p-8 mb-8 scroll-mt-6">
            <div class="flex items-center mb-4">
                <div class="bg-red-100 text-red-600 p-3 rounded-lg mr-4"><i class="fas fa-shield-alt text-xl"></i></div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">6. Security</h2>
                    <p class="text-gray-500 text-sm">Sidebar: <strong>Settings → Security</strong></p>
                </div>
            </div>

            <p class="text-gray-600 text-sm leading-relaxed mb-6">
                Update your account password. Any user (including managers) can change their own password here.
            </p>

            {!! $steps([
                '<strong>Current Password</strong> — enter your existing password.',
                '<strong>New Password</strong> — choose a strong password (mix of letters, numbers, and symbols).',
                '<strong>Confirm New Password</strong> — repeat the new password exactly.',
                'Click <strong>Update Password</strong>. You will stay signed in and should use the new password next time.',
            ]) !!}

            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-700"><i class="fas fa-exclamation-triangle mr-2"></i>
                    If you forget your password, use the <strong>Forgot Password</strong> link on the login page to
                    reset it via email instead of contacting support.
                </p>
            </div>
        </section>

        {{-- Best practices --}}
        <section class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl p-8 mb-10">
            <h2 class="text-2xl font-bold mb-4">Best Practices</h2>
            <ul class="space-y-3 text-blue-100 text-sm">
                <li><i class="fas fa-check-circle mr-2 text-green-300"></i> Set up <strong>Organization Settings</strong> first so invoices and emails use the correct company details.</li>
                <li><i class="fas fa-check-circle mr-2 text-green-300"></i> Configure <strong>Mail</strong> and send a <strong>test email</strong> before sending real invoices.</li>
                <li><i class="fas fa-check-circle mr-2 text-green-300"></i> Keep Stripe keys and webhook secrets private; the eye buttons exist for your convenience only.</li>
                <li><i class="fas fa-check-circle mr-2 text-green-300"></i> Choose a <strong>Base Currency</strong> once and avoid changing it after invoices exist.</li>
                <li><i class="fas fa-check-circle mr-2 text-green-300"></i> Use a strong, unique password and change it regularly from the <strong>Security</strong> tab.</li>
                <li><i class="fas fa-check-circle mr-2 text-green-300"></i> Test webhooks in a sandbox environment before enabling them in production.</li>
            </ul>
        </section>

        {{-- Still stuck --}}
        <div class="bg-white shadow-xl rounded-2xl p-8 text-center">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Still Stuck?</h2>
            <p class="text-gray-500 mb-6">We're here to help you configure your account without delay.</p>
            <a href="mailto:support@{{ $globalSettings->company_name ?? config('app.name') }}.com"
               class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition">
                <i class="fas fa-envelope mr-2"></i> Contact Support
            </a>
        </div>
    </div>
@endsection
