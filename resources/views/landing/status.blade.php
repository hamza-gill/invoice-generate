@extends('landing.subpage')

@section('title', 'System Status - Inveqi')
@section('meta_description', 'Check the live status of the Inveqi platform, payments, email delivery and databases.')
@section('meta_keywords', 'invoice software status, invoicing uptime, platform status, invoicing service status')
@section('page-kicker', 'Resources')
@section('page-title', 'System Status')
@section('page-subtitle', 'Live status of the Inveqi platform and its services.')

@section('page-content')
<div class="mx-auto max-w-2xl space-y-4">
    @foreach([
        ['fa-globe', 'Website & App', 'Operational'],
        ['fa-file-invoice', 'Invoices & Estimates', 'Operational'],
        ['fa-credit-card', 'Stripe Payments', 'Operational'],
        ['fa-envelope', 'Email Delivery', 'Operational'],
        ['fa-database', 'Database', 'Operational'],
    ] as $s)
    <div class="flex items-center justify-between rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl gradient-primary text-white">
                <i class="fas {{ $s[0] }}"></i>
            </div>
            <span class="font-medium text-gray-900">{{ $s[1] }}</span>
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            {{ $s[2] }}
        </span>
    </div>
    @endforeach
    <p class="pt-4 text-center text-xs text-gray-400">Last checked: {{ now()->format('F j, Y g:i A') }}</p>
</div>
@endsection
