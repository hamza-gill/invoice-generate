@extends('landing.subpage')

@section('title', 'API Reference - Inveqi')
@section('meta_description', 'Build custom integrations with the Inveqi REST API. Create invoices, manage customers and products programmatically.')
@section('meta_keywords', 'invoicing API, invoice REST API, invoicing integration API, developer API, invoice software API')
@section('page-kicker', 'Resources')
@section('page-title', 'API Reference')
@section('page-subtitle', 'Build custom integrations with the Inveqi REST API.')

@section('page-content')
<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900">Getting started</h2>
        <p class="mt-2 text-sm text-gray-500 leading-relaxed">
            Our REST API lets you create invoices, manage customers and products, and check payment status programmatically.
            Authenticate with an API key sent in the <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-700">Authorization</code> header.
        </p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-card">
        <div class="flex items-center gap-1.5 border-b border-gray-200 bg-gray-50/60 px-4 py-3">
            <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-yellow-400"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-green-400"></span>
            <span class="ml-3 text-xs text-gray-400">GET /api/v1/invoices</span>
        </div>
        <div class="p-6 text-sm text-gray-600">
            <pre class="overflow-x-auto text-xs leading-relaxed text-gray-700"><code>{
    "id": "inv_1234",
    "number": "INV-2026-001",
    "status": "paid",
    "total": 1250.00,
    "customer": {
        "name": "Acme Inc",
        "email": "billing@acme.com"
    },
    "items": [
        { "description": "Consulting", "amount": 1250.00 }
    ]
}</code></pre>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        @foreach([
            ['GET', '/api/v1/invoices', 'List invoices'],
            ['POST', '/api/v1/invoices', 'Create an invoice'],
            ['GET', '/api/v1/customers', 'List customers'],
            ['POST', '/api/v1/customers', 'Create a customer'],
            ['GET', '/api/v1/products', 'List products'],
            ['POST', '/api/v1/products', 'Create a product'],
        ] as $e)
        <div class="flex items-center gap-4 rounded-2xl border border-gray-200 bg-white p-5 shadow-card">
            <span class="rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-600">{{ $e[0] }}</span>
            <code class="text-xs text-gray-700">{{ $e[1] }}</code>
            <span class="ml-auto text-xs text-gray-400">{{ $e[2] }}</span>
        </div>
        @endforeach
    </div>

    <p class="text-center text-sm text-gray-500">
        Need API access? <a href="{{ route('contact') }}" class="text-blue-600 hover:underline">Contact us</a> to request credentials.
    </p>
</div>
@endsection
