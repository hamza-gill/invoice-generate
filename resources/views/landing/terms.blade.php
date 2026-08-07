@extends('layouts.marketing')

@section('title', 'Terms of Service - Inveqi')
@section('meta_description', 'Read the Inveqi Terms of Service covering account use, subscriptions, payments, acceptable use and more.')
@section('meta_keywords', 'terms of service invoicing, invoice software terms, invoicing terms and conditions')

@section('content')
@include('landing.partials.nav')

<section class="relative overflow-hidden py-12 sm:py-16">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-30"></div>

    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Legal</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Terms of Service</h1>
            <p class="mt-4 text-sm text-gray-500">Last updated: {{ date('F j, Y') }}</p>
        </div>

        <div class="mt-10 space-y-8 rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-10 shadow-card text-sm leading-relaxed text-gray-600">
            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">1. Acceptance of Terms</h2>
                <p>By accessing or using the Inveqi service, you agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree, please do not use the service.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">2. Description of Service</h2>
                <p>Inveqi provides online invoicing, estimates, recurring billing, and related tools to help businesses create, send, and manage invoices and payments.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">3. Accounts &amp; Registration</h2>
                <p>You must provide accurate and complete information when creating an account. You are responsible for safeguarding your credentials and for all activity that occurs under your account. Notify us immediately of any unauthorized use.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">4. Subscriptions &amp; Payments</h2>
                <p>Some features are available under paid subscription plans. Fees are billed in advance on a recurring basis and are non-refundable except as required by law. You may cancel your subscription at any time; access continues until the end of the current billing period.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">5. Acceptable Use</h2>
                <p>You agree not to misuse the service, including by transmitting unlawful content, attempting to access systems without authorization, interfering with the service, or using the service in violation of applicable law.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">6. Intellectual Property</h2>
                <p>All software, designs, text, graphics, and other content provided as part of the service are owned by Inveqi or its licensors and are protected by intellectual property laws. You retain all rights to the data and documents you create.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">7. Termination</h2>
                <p>We may suspend or terminate your access if you violate these Terms or if required by law. Upon termination, your right to use the service ends, and we may delete your data after a reasonable period in accordance with our Privacy Policy.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">8. Disclaimers</h2>
                <p>The service is provided "as is" and "as available" without warranties of any kind, whether express or implied, including but not limited to warranties of merchantability, fitness for a particular purpose, and non-infringement.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">9. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, Inveqi shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">10. Changes to These Terms</h2>
                <p>We may update these Terms from time to time. We will notify you of material changes by posting the updated Terms on this page. Continued use of the service after changes constitutes acceptance.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">11. Governing Law</h2>
                <p>These Terms are governed by the laws of the jurisdiction in which Inveqi is established, without regard to conflict of law principles.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">12. Contact Us</h2>
                <p>If you have questions about these Terms, please <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">contact us</a>.</p>
            </section>
        </div>
    </div>
</section>

@include('landing.partials.footer')
@endsection
