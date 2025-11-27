@extends('layouts.auth.app')

@section('title', 'Help & Support - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- 🌟 Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900">Help & Support</h1>
            <p class="text-gray-500 mt-3 text-lg">
                We're here to help you get the most out of <span class="font-semibold text-blue-600">{{ $globalSettings->company_name ?? config('app.name') }}</span>.
            </p>
        </div>

        <!-- 🔹 Hero Card -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl shadow-lg p-8 mb-10 text-center">
            <h2 class="text-2xl font-semibold mb-2">Need quick help?</h2>
            <p class="text-blue-100 mb-6">Our support team is always ready to assist you with your questions and technical issues.</p>
            <div class="flex justify-center gap-4">
                <a href="mailto:support@{{ $globalSettings->company_name ?? config('app.name') }}.com"
                   class="bg-white text-blue-700 font-medium px-6 py-3 rounded-lg shadow hover:bg-blue-50 transition">
                    ✉️ Email Support
                </a>
                <a href="#faq"
                   class="bg-transparent border border-white px-6 py-3 rounded-lg font-medium hover:bg-white hover:text-blue-700 transition">
                    📘 Browse FAQs
                </a>
            </div>
        </div>

        <!-- 🔸 Section: Common Topics -->
        <div class="grid md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-lg mr-3">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Invoices & Billing</h3>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Learn how to create, manage, and send invoices. Understand billing cycles and tax setup in {{ $globalSettings->company_name ?? config('app.name') }}.
                </p>
            </div>

            <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <div class="bg-green-100 text-green-600 p-3 rounded-lg mr-3">
                        <i class="fas fa-plug text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Integrations</h3>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Connect {{ $globalSettings->company_name ?? config('app.name') }} with Stripe, manage API keys, and set up payment automation easily.
                </p>
            </div>

            <div class="bg-white shadow-md rounded-xl p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-lg mr-3">
                        <i class="fas fa-user-shield text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">Account & Security</h3>
                </div>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Manage your account details, password resets, and two-factor authentication securely.
                </p>
            </div>
        </div>

        <!-- 🔹 FAQ Section -->
        <div id="faq" class="mb-12">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Frequently Asked Questions</h2>

            <div class="space-y-4">
                <details class="bg-white shadow rounded-lg p-5">
                    <summary class="font-medium text-gray-800 cursor-pointer">💡 How can I create a new invoice?</summary>
                    <p class="mt-2 text-gray-600 text-sm leading-relaxed">
                        Go to the “Invoices” tab in the sidebar, click “Create Invoice”, fill out product and client details, and hit save.
                    </p>
                </details>

                <details class="bg-white shadow rounded-lg p-5">
                    <summary class="font-medium text-gray-800 cursor-pointer">🔐 How do I reset my password?</summary>
                    <p class="mt-2 text-gray-600 text-sm leading-relaxed">
                        You can reset your password from the “Security” section under Settings or via the forgot password link on the login page.
                    </p>
                </details>

                <details class="bg-white shadow rounded-lg p-5">
                    <summary class="font-medium text-gray-800 cursor-pointer">💳 How do I connect my Stripe account?</summary>
                    <p class="mt-2 text-gray-600 text-sm leading-relaxed">
                        Visit the “Integrations” tab in Settings and enter your Stripe public & secret keys. Save changes to activate the connection.
                    </p>
                </details>

                <details class="bg-white shadow rounded-lg p-5">
                    <summary class="font-medium text-gray-800 cursor-pointer">📩 How can I contact support?</summary>
                    <p class="mt-2 text-gray-600 text-sm leading-relaxed">
                        Simply email us at <a href="mailto:support@'{{ $globalSettings->company_name ?? config('app.name') }}'.com" class="text-blue-600 underline">support@ {{ $globalSettings->company_name ?? config('app.name') }}.com</a>.
                        Our response time is usually under 24 hours.
                    </p>
                </details>
            </div>
        </div>

        <!-- 🧭 Quick Contact Card -->
        <div class="bg-white shadow-xl rounded-2xl p-8 text-center mb-12">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Still Need Assistance?</h2>
            <p class="text-gray-500 mb-6">We’re happy to help. Reach out to our team directly.</p>
            <div class="flex justify-center gap-4">
                <a href="mailto:support@{{ $globalSettings->company_name ?? config('app.name') }}.com"
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition">
                    <i class="fas fa-envelope mr-2"></i> Contact Support
                </a>
                <a href="#"
                   class="border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition">
                    <i class="fas fa-comments mr-2"></i> Open Live Chat
                </a>
            </div>
        </div>

        <!-- 🌐 Footer Info -->
        <div class="text-center text-gray-500 text-sm pb-6">
            <p>© {{ date('Y') }} {{ $globalSettings->company_name ?? config('app.name') }} — All Rights Reserved.</p>
            <p>Built to simplify invoicing and business operations.</p>
        </div>
    </div>
@endsection
