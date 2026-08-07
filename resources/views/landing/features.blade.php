@extends('landing.subpage')

@section('title', 'Features - Inveqi')
@section('meta_description', 'Explore Inveqi features: instant invoicing, recurring billing, estimates & quotes, Stripe payments, reports, team access, PDF export and email invoicing.')
@section('meta_keywords', 'invoice features, recurring invoices, estimate software, quote generator, online payment invoicing, invoice reports, invoice templates, invoice builder')
@section('page-kicker', 'Product')
@section('page-title', 'Everything you need to get paid')
@section('page-subtitle', 'Powerful invoicing tools for modern businesses.')

@section('page-content')
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @foreach([
        ['fa-bolt', 'Instant Invoicing', 'Create professional invoices in seconds with our drag-and-drop builder and 10+ templates.'],
        ['fa-repeat', 'Recurring Billing', 'Automate repeat invoices for subscriptions and retainers so you never miss a payment.'],
        ['fa-file-alt', 'Estimates & Quotes', 'Send polished estimates with one-click approval and convert them to invoices instantly.'],
        ['fa-credit-card', 'Stripe Payments', 'Accept credit card payments online and get paid faster with secure Stripe integration.'],
        ['fa-chart-line', 'Reports & Analytics', 'Track revenue, outstanding balances, and payment trends with built-in dashboards.'],
        ['fa-users', 'Team Access', 'Invite your team with role-based permissions for admins and accountants.'],
        ['fa-cloud', 'PDF Export', 'Download beautifully formatted PDF invoices and estimates for records or email.'],
        ['fa-envelope', 'Email Invoicing', 'Send invoices and estimates directly from your workspace with tracked delivery.'],
        ['fa-cubes', 'Products & Catalog', 'Maintain a reusable product and service catalog with pricing and tax rules.'],
    ] as $f)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:shadow-glow">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl gradient-primary text-white">
            <i class="fas {{ $f[0] }}"></i>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $f[1] }}</h3>
        <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $f[2] }}</p>
    </div>
    @endforeach
</div>
@endsection
