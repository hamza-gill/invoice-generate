@extends('landing.subpage')

@section('title', 'Help Center - Inveqi')
@section('meta_description', 'Get help with Inveqi: FAQs about invoices, Stripe payments, recurring billing, team members and PDF export.')
@section('meta_keywords', 'invoice help, invoicing FAQ, invoice software support, invoice help center')
@section('page-kicker', 'Resources')
@section('page-title', 'Help Center')
@section('page-subtitle', 'Answers to common questions and where to find support.')

@section('page-content')
<div class="grid gap-6 md:grid-cols-2">
    @foreach([
        ['fa-file-invoice', 'How do I create an invoice?', 'Open the Invoices section, click Create, add items, and send. See the docs for a step-by-step guide.'],
        ['fa-credit-card', 'How do I accept payments?', 'Enable the Stripe gateway from Settings &rarr; Integrations and add your API keys.'],
        ['fa-repeat', 'How do I set up recurring billing?', 'Create a recurring invoice from the Recurring section and choose a schedule.'],
        ['fa-users', 'How do I add team members?', 'Go to Settings &rarr; Users and invite your teammates with a role.'],
        ['fa-file-download', 'Can I export invoices to PDF?', 'Yes. Open any invoice and use the Download button to export a formatted PDF.'],
        ['fa-question-circle', 'How do I get more help?', 'Reach out through the contact form and our team will respond within 1&ndash;2 business days.'],
    ] as $h)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl gradient-primary text-white">
                <i class="fas {{ $h[0] }}"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $h[1] }}</h3>
        </div>
        <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $h[2] }}</p>
    </div>
    @endforeach
</div>
<p class="mt-8 text-center text-sm text-gray-500">
    Can't find an answer? <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">Contact support</a>.
</p>
@endsection
