@extends('layouts.marketing')

@section('content')
@include('landing.partials.nav')

<section class="relative overflow-hidden py-12 sm:py-16">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-30"></div>
    <div class="mx-auto max-w-5xl px-4 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">@yield('page-kicker', 'Inveqi')</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">@yield('page-title')</h1>
            <p class="mt-4 text-base sm:text-lg text-gray-500">@yield('page-subtitle', '')</p>
        </div>
        <div class="mt-12">
            @yield('page-content')
        </div>
    </div>
</section>

@include('landing.partials.footer')
@endsection
