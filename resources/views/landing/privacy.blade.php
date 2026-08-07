@extends('layouts.marketing')

@section('title', 'Privacy Policy - Inveqi')
@section('meta_description', 'Read the Inveqi Privacy Policy to learn how we collect, use and protect your information.')
@section('meta_keywords', 'privacy policy invoicing, invoice software privacy, invoicing data protection')

@section('content')
@include('landing.partials.nav')

<section class="relative overflow-hidden py-12 sm:py-16">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-30"></div>

    <div class="mx-auto max-w-3xl px-4 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Legal</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">Privacy Policy</h1>
            <p class="mt-4 text-sm text-gray-500">Last updated: {{ date('F j, Y') }}</p>
        </div>

        <div class="mt-10 space-y-8 rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-10 shadow-card text-sm leading-relaxed text-gray-600">
            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">1. Information We Collect</h2>
                <p>We collect information you provide directly, such as your name, email address, company details, phone number, and billing information. We also collect data you enter while using the service, including customer and invoice information, and technical data such as IP address, browser type, and usage statistics.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">2. How We Use Information</h2>
                <p>We use the information we collect to provide, maintain, and improve the service; process transactions; send transactional and marketing communications; and respond to your requests and support inquiries.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">3. Cookies &amp; Similar Technologies</h2>
                <p>We use cookies and similar technologies to keep you signed in, remember preferences, and understand how the service is used. You can control cookies through your browser settings, though some features may not function properly if cookies are disabled.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">4. Sharing of Information</h2>
                <p>We do not sell your personal information. We share information only with service providers who help us operate the service (such as hosting, email delivery, and payment processing), as required by law, or to protect the rights and safety of our users and the public.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">5. Payment Processing</h2>
                <p>Payments are processed by trusted third-party providers, such as Stripe. Your payment card details are handled directly by these providers in accordance with their own privacy and security policies; we do not store full payment card numbers.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">6. Data Retention</h2>
                <p>We retain your information for as long as your account is active or as needed to provide the service. When you close your account, we delete or anonymize your data within a reasonable period, unless we are required to retain it for legal, accounting, or security reasons.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">7. Security</h2>
                <p>We implement reasonable technical and organizational measures to protect your information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission or storage is completely secure.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">8. Your Rights</h2>
                <p>Depending on your jurisdiction, you may have the right to access, correct, delete, or port your personal information, and to object to or restrict certain processing. To exercise these rights, please contact us using the details below.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">9. Children's Privacy</h2>
                <p>The service is not directed to children under the age of 13, and we do not knowingly collect personal information from children. If you believe a child has provided us information, please contact us so we can delete it.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">10. Changes to This Policy</h2>
                <p>We may update this Privacy Policy from time to time. We will post any changes on this page and update the "Last updated" date. Significant changes will be communicated to you through the service.</p>
            </section>

            <section>
                <h2 class="mb-3 text-lg font-semibold text-gray-900">11. Contact Us</h2>
                <p>If you have questions about this Privacy Policy or your personal data, please <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">contact us</a>.</p>
            </section>
        </div>
    </div>
</section>

@include('landing.partials.footer')
@endsection
