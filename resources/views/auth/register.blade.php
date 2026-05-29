@extends('layouts.guest.auth')

@section('title', 'Register - ReconX')
@section('auth-heading', 'Start your free trial')
@section('auth-subheading', 'Create your workspace in minutes. Invoices, estimates, templates, and Stripe payments — no credit card required.')

@section('auth-alt-link')
    <span class="hidden sm:inline text-gray-500">Have an account?</span>
    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-700">Sign in</a>
@endsection

@section('content')
    <div class="rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 shadow-lg backdrop-blur" style="box-shadow: var(--shadow-card);">
        <div class="mb-6 lg:hidden text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl gradient-primary text-white">
                <i class="fas fa-bolt"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Create account</h2>
            <p class="mt-1 text-sm text-gray-500">14-day free trial · No credit card</p>
        </div>

        <div class="hidden lg:block mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Create your account</h2>
            <p class="mt-1 text-sm text-gray-500">Start your 14-day free trial today</p>
        </div>

        @include('layouts.errors')

        <form class="space-y-4" action="{{ route('submit.register') }}" method="post">
            @csrf

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Company / business name</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" required
                       class="auth-input !pl-4 @error('company_name') border-red-400 @enderror"
                       placeholder="Your company name" style="padding-left: 1rem;">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">First name</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="auth-input !pl-4" placeholder="First name" style="padding-left: 1rem;">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Last name</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="auth-input !pl-4" placeholder="Last name" style="padding-left: 1rem;">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="auth-input !pl-4" placeholder="you@company.com" style="padding-left: 1rem;">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                       class="auth-input !pl-4" placeholder="+1 (555) 000-0000" style="padding-left: 1rem;">
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" required
                           class="auth-input !pl-4" placeholder="Min. 8 characters" style="padding-left: 1rem;">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Confirm password</label>
                    <input type="password" name="password_confirmation" required
                           class="auth-input !pl-4" placeholder="Repeat password" style="padding-left: 1rem;">
                </div>
            </div>

            <div class="space-y-2.5 rounded-xl border border-gray-100 bg-gray-50/80 p-4 text-sm text-gray-600">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="terms" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600" required>
                    <span>I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a></span>
                </label>
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="privacy" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600" required>
                    <span>I agree to the <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a></span>
                </label>
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="sharing" class="mt-0.5 h-4 w-4 rounded border-gray-300 text-blue-600" required>
                    <span>I agree to data processing for account setup</span>
                </label>
            </div>

            <button type="submit" class="auth-btn">
                Create Account &amp; Start Trial
            </button>

            <p class="text-center text-sm text-gray-500 lg:hidden">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-700">Sign in</a>
            </p>
        </form>
    </div>
@endsection
