@extends('layouts.marketing')

@section('title', 'Contact Us - Inveqi')
@section('meta_description', 'Contact Inveqi to request a new feature, module or integration. Tell us what you need built and we will shape the product around your workflows.')
@section('meta_keywords', 'contact invoice software, request feature, invoicing integration request, invoice software feedback')

@section('content')
@include('landing.partials.nav', ['active' => 'contact'])

<section class="relative overflow-hidden py-12 sm:py-16">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-30"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Contact</p>
            <h1 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl md:text-5xl">Tell us what you need built</h1>
            <p class="mt-4 text-base sm:text-lg text-gray-500">
                Request a new feature, module, or integration. Share your workflow and we'll help shape the product around real customer use cases.
            </p>
        </div>

        @if(session('success'))
            <div class="mx-auto mt-8 max-w-3xl rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 flex items-start gap-3">
                <i class="fas fa-check-circle text-emerald-600 mt-0.5"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="mt-10 grid gap-8 lg:grid-cols-5 lg:gap-12">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-gray-200/80 bg-white p-6 shadow-card">
                    <h2 class="text-lg font-semibold text-gray-900">What you can request</h2>
                    <ul class="mt-4 space-y-4 text-sm text-gray-600">
                        <li class="flex gap-3">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-puzzle-piece text-xs"></i></span>
                            <div><strong class="text-gray-800">New module</strong><br>Need inventory, projects, CRM, or another workflow? Describe the module end-to-end.</div>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-lightbulb text-xs"></i></span>
                            <div><strong class="text-gray-800">Feature enhancement</strong><br>Improve invoices, estimates, recurring billing, templates, or reporting.</div>
                        </li>
                        <li class="flex gap-3">
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-plug text-xs"></i></span>
                            <div><strong class="text-gray-800">Integrations</strong><br>QuickBooks, Xero, Zapier, custom APIs, or payment providers.</div>
                        </li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-6 text-sm text-gray-600">
                    <p class="font-medium text-gray-800">Tip for faster review</p>
                    <p class="mt-2">Include who will use it, what problem it solves, and any must-have fields or steps. Screenshots or sample documents help too.</p>
                </div>
            </div>

            <div class="lg:col-span-3">
                <form action="{{ route('contact.store') }}" method="POST" class="rounded-2xl border border-gray-200/80 bg-white p-6 sm:p-8 shadow-card space-y-5">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Request type <span class="text-red-500">*</span></label>
                            <select name="request_type" id="request_type" required class="contact-field @error('request_type') border-red-400 @enderror">
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" @selected(old('request_type', 'feature_request') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('request_type')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div id="module_name_wrap" class="sm:col-span-2 hidden">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Module / area name <span class="text-red-500">*</span></label>
                            <input type="text" name="module_name" id="module_name" value="{{ old('module_name') }}"
                                   class="contact-field @error('module_name') border-red-400 @enderror"
                                   placeholder="e.g. Project costing, Vendor portal, Inventory">
                            @error('module_name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Title / summary <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   class="contact-field @error('title') border-red-400 @enderror"
                                   placeholder="Short summary of what you need">
                            @error('title')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Your name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $prefill['name'] ?? '') }}" required class="contact-field @error('name') border-red-400 @enderror">
                            @error('name')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $prefill['email'] ?? '') }}" required class="contact-field @error('email') border-red-400 @enderror">
                            @error('email')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Company</label>
                            <input type="text" name="company" value="{{ old('company', $prefill['company'] ?? '') }}" class="contact-field" placeholder="Optional">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $prefill['phone'] ?? '') }}" class="contact-field" placeholder="Optional">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" class="contact-field">
                                @foreach($priorities as $value => $label)
                                    <option value="{{ $value }}" @selected(old('priority', 'normal') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Requirements &amp; details <span class="text-red-500">*</span></label>
                            <textarea name="requirements" rows="5" required
                                      class="contact-field resize-y min-h-[120px] @error('requirements') border-red-400 @enderror"
                                      placeholder="Describe features, screens, fields, rules, and how users should interact with it.">{{ old('requirements') }}</textarea>
                            @error('requirements')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Business use case</label>
                            <textarea name="use_case" rows="4"
                                      class="contact-field resize-y min-h-[96px]"
                                      placeholder="Who uses this today? What problem does it solve? What happens if we don't build it?">{{ old('use_case') }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl gradient-primary px-6 py-3.5 text-sm font-semibold text-white shadow-soft transition hover:opacity-95">
                        Submit request
                    </button>
                    <p class="text-center text-xs text-gray-400">We typically respond within 1–2 business days.</p>
                </form>
            </div>
        </div>
    </div>
</section>

@include('landing.partials.footer')

<style>
    .contact-field {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-size: 0.9375rem;
        color: #111827;
        background: #fff;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .contact-field:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('request_type');
        const moduleWrap = document.getElementById('module_name_wrap');
        const moduleInput = document.getElementById('module_name');

        function toggleModuleField() {
            const show = typeSelect.value === 'new_module';
            moduleWrap.classList.toggle('hidden', !show);
            moduleInput.required = show;
        }

        typeSelect.addEventListener('change', toggleModuleField);
        toggleModuleField();
    });
</script>
@endsection
