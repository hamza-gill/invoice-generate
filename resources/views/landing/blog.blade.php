@extends('landing.subpage')

@section('title', 'Blog - Inveqi')
@section('meta_description', 'Read the Inveqi blog for invoicing tips, payment best practices and small business finance advice.')
@section('meta_keywords', 'invoicing tips, invoice blog, small business finance, getting paid faster, invoice best practices')
@section('page-kicker', 'Company')
@section('page-title', 'Blog')
@section('page-subtitle', 'Insights and tips for invoicing, payments, and small business finance.')

@section('page-content')
<div class="grid gap-6 md:grid-cols-3">
    @foreach([
        ['How to get paid faster with recurring invoices', 'Automate repeat billing and reduce the time you spend chasing payments.', 'July 2026', 'fa-repeat'],
        ['10 invoice templates that look professional', 'A look at our built-in templates and how to choose the right one for your brand.', 'June 2026', 'fa-file-alt'],
        ['Estimates vs quotes: what is the difference?', 'Understand the difference and when to use each one with your customers.', 'May 2026', 'fa-question-circle'],
    ] as $p)
    <a href="#" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:shadow-glow">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl gradient-primary text-white">
            <i class="fas {{ $p[3] }}"></i>
        </div>
        <h3 class="mt-4 text-lg font-semibold text-gray-900 group-hover:text-blue-600">{{ $p[0] }}</h3>
        <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $p[1] }}</p>
        <p class="mt-4 text-xs text-gray-400">{{ $p[2] }}</p>
    </a>
    @endforeach
</div>
@endsection
