@extends('landing.subpage')

@section('title', 'Careers - Inveqi')
@section('meta_description', 'Join the Inveqi team. We build the future of invoicing software. See our open roles and culture.')
@section('meta_keywords', 'invoice software jobs, invoicing careers, startup jobs, invoice company careers')
@section('page-kicker', 'Company')
@section('page-title', 'Careers')
@section('page-subtitle', 'Come build the future of invoicing with us.')

@section('page-content')
<div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-card text-center">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl gradient-primary text-white">
        <i class="fas fa-briefcase"></i>
    </div>
    <h2 class="mt-4 text-xl font-semibold text-gray-900">Open positions</h2>
    <p class="mx-auto mt-2 max-w-md text-sm text-gray-500">
        We're a small, fast-moving team. We don't have any open roles right now, but we'd love to hear from you.
    </p>
    <a href="{{ route('contact') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg gradient-primary px-5 py-2.5 text-sm font-medium text-white shadow-soft hover:opacity-90">
        Get in touch <i class="fas fa-arrow-right text-xs"></i>
    </a>
</div>
@endsection
