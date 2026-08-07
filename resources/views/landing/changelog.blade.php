@extends('landing.subpage')

@section('title', 'Changelog - Inveqi')
@section('meta_description', 'Stay up to date with the latest Inveqi product updates, new features and improvements shipped to our invoicing software.')
@section('meta_keywords', 'invoice software updates, invoicing changelog, product updates, new features invoicing')
@section('page-kicker', 'Product')
@section('page-title', 'Changelog')
@section('page-subtitle', 'The latest updates, improvements, and fixes shipped to Inveqi.')

@section('page-content')
<div class="space-y-6">
    @foreach([
        ['version' => 'v1.4.0', 'date' => 'July 2026', 'items' => ['Recurring invoice automation for paid plans', 'New PDF template themes', 'Faster global search', 'Improved mobile layout for estimates']],
        ['version' => 'v1.3.0', 'date' => 'June 2026', 'items' => ['Stripe payment gateway per organization', 'Role-based team permissions', 'Product catalog import via CSV']],
        ['version' => 'v1.2.0', 'date' => 'May 2026', 'items' => ['Multi-tenant workspaces', 'Platform admin console', 'Subscription plans and trials']],
        ['version' => 'v1.1.0', 'date' => 'April 2026', 'items' => ['10+ invoice templates', 'Custom invoice terms and notes', 'Invoice due date reminders']],
        ['version' => 'v1.0.0', 'date' => 'March 2026', 'items' => ['Initial release — invoices, estimates, and PDF export']],
    ] as $entry)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-lg font-semibold text-gray-900">{{ $entry['version'] }}</h3>
            <span class="text-xs text-gray-400">{{ $entry['date'] }}</span>
        </div>
        <ul class="mt-3 space-y-2 text-sm text-gray-600">
            @foreach($entry['items'] as $item)
            <li class="flex items-start gap-2">
                <i class="fas fa-check-circle mt-0.5 text-blue-500 text-xs"></i>
                <span>{{ $item }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endforeach
</div>
@endsection
