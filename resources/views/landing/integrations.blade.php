@extends('landing.subpage')

@section('title', 'Integrations - Inveqi')
@section('meta_description', 'Connect Inveqi with Stripe, QuickBooks, Xero, Zapier, Gmail, Slack and more. All integrations are coming soon.')
@section('meta_keywords', 'invoicing integrations, Stripe invoicing, QuickBooks integration, Xero integration, accounting software integrations, invoice software')
@section('page-kicker', 'Product')
@section('page-title', 'Integrations')
@section('page-subtitle', 'Connect Inveqi with the tools you already use every day. Integrations are coming soon.')

@section('page-content')
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @foreach([
        ['fa-credit-card', 'Stripe', 'Accept payments online and automate reconciliation.'],
        ['fa-book', 'QuickBooks', 'Sync invoices and customers with QuickBooks.'],
        ['fa-chart-bar', 'Xero', 'Keep your accounting in sync with Xero.'],
        ['fa-bolt', 'Zapier', 'Automate workflows across 5,000+ apps.'],
        ['fa-code', 'REST API', 'Build custom integrations with our API.'],
        ['fa-envelope', 'Gmail & Outlook', 'Send invoices straight from your inbox.'],
        ['fa-cloud', 'Google Drive', 'Back up PDFs and exports to Drive.'],
        ['fa-slack', 'Slack', 'Get notifications for payments and activity.'],
    ] as $i)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:shadow-glow">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl gradient-primary text-white">
                <i class="fas {{ $i[0] }}"></i>
            </div>
            <div class="min-w-0">
                <h3 class="text-lg font-semibold text-gray-900">{{ $i[1] }}</h3>
                <span class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-medium text-amber-700">
                    <i class="fas fa-clock text-[10px]"></i>
                    Coming soon
                </span>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-500 leading-relaxed">{{ $i[2] }}</p>
    </div>
    @endforeach
</div>
<p class="mt-8 text-center text-sm text-gray-500">
    Looking for something else? <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">Request an integration</a>.
</p>
@endsection
