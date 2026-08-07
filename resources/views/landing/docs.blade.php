@extends('landing.subpage')

@section('title', 'Documentation - Inveqi')
@section('meta_description', 'Inveqi documentation and user guides: getting started, customers, invoices, estimates, recurring billing and products.')
@section('meta_keywords', 'invoice software guide, invoicing help, invoice documentation, how to invoice, invoice software tutorial')
@section('page-kicker', 'Resources')
@section('page-title', 'Documentation')
@section('page-subtitle', 'Everything you need to get started and make the most of Inveqi.')

@section('page-content')
<div class="grid gap-6 md:grid-cols-2">
    @foreach([
        ['fa-rocket', 'Getting started', 'Create your account, set up your workspace, and create your first invoice.', 'docs'],
        ['fa-users', 'Customers', 'Add, import, and manage your customer list.', 'docs'],
        ['fa-file-invoice', 'Invoices', 'Create, send, and track invoices end to end.', 'docs'],
        ['fa-file-alt', 'Estimates', 'Send estimates and convert them to invoices with one click.', 'docs'],
        ['fa-repeat', 'Recurring billing', 'Set up automated recurring invoices for subscriptions.', 'docs'],
        ['fa-cubes', 'Products & catalog', 'Build a reusable catalog of products and services.', 'docs'],
    ] as $d)
    <a href="#" class="group flex items-start gap-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:shadow-glow">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl gradient-primary text-white">
            <i class="fas {{ $d[0] }}"></i>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600">{{ $d[1] }}</h3>
            <p class="mt-1 text-sm text-gray-500 leading-relaxed">{{ $d[2] }}</p>
        </div>
    </a>
    @endforeach
</div>
<p class="mt-8 text-center text-sm text-gray-500">
    Stuck on something? Visit the <a href="{{ route('help-center') }}" class="text-blue-600 hover:underline">help center</a> or <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">contact us</a>.
</p>
@endsection
