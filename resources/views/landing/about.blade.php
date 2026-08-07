@extends('landing.subpage')

@section('title', 'About Us - Inveqi')
@section('meta_description', 'Inveqi builds simple, beautiful invoicing software for businesses. Learn about our mission and values.')
@section('meta_keywords', 'about invoice software, invoicing company, invoice software team, invoice management company')
@section('page-kicker', 'Company')
@section('page-title', 'About Inveqi')
@section('page-subtitle', 'We build simple, beautiful invoicing software for businesses everywhere.')

@section('page-content')
<div class="space-y-12">
    <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-card">
        <h2 class="text-xl font-semibold text-gray-900">Our mission</h2>
        <p class="mt-3 text-sm text-gray-600 leading-relaxed">
            Getting paid shouldn't be the hardest part of running a business. Inveqi exists to make invoicing,
            estimates, and recurring billing fast, reliable, and effortless — so you can focus on the work that matters.
        </p>
    </div>

    <div>
        <h2 class="text-xl font-semibold text-gray-900">Our values</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach([
                ['fa-heart', 'Customer first', 'Every decision starts with the people who use our product.'],
                ['fa-shield-alt', 'Trust & security', 'Your data is protected with best-in-class security and privacy practices.'],
                ['fa-lightbulb', 'Keep it simple', 'Powerful tools should still be easy to use. We obsess over the details.'],
            ] as $v)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl gradient-primary text-white">
                    <i class="fas {{ $v[0] }}"></i>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">{{ $v[1] }}</h3>
                <p class="mt-2 text-sm text-gray-500 leading-relaxed">{{ $v[2] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-card text-center">
        <h2 class="text-xl font-semibold text-gray-900">Want to join us?</h2>
        <p class="mt-2 text-sm text-gray-500">We're always looking for talented people.</p>
        <a href="{{ route('careers') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg gradient-primary px-5 py-2.5 text-sm font-medium text-white shadow-soft hover:opacity-90">
            See open roles <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</div>
@endsection
