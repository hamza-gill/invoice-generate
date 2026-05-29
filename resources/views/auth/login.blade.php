@extends('layouts.guest.auth')

@section('title', 'Login - ' . ($globalSettings->company_name ?? 'ReconX'))
@section('auth-heading', 'Welcome back')
@section('auth-subheading', 'Sign in to manage invoices, estimates, recurring billing, and get paid faster with Stripe.')

@section('auth-alt-link')
    <span class="hidden sm:inline text-gray-500">No account?</span>
    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700">Start free trial</a>
@endsection

@section('content')
    <div class="rounded-2xl border border-white/80 bg-white/90 p-6 sm:p-8 shadow-lg backdrop-blur" style="box-shadow: var(--shadow-card);">
        <div class="mb-6 lg:hidden text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl gradient-primary text-white">
                <i class="fas fa-bolt"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Sign in</h2>
            <p class="mt-1 text-sm text-gray-500">Access your {{ $globalSettings->company_name ?? 'ReconX' }} account</p>
        </div>

        <div class="hidden lg:block mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Sign in</h2>
            <p class="mt-1 text-sm text-gray-500">Enter your credentials to continue</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3">
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="loginForm" class="space-y-4" method="POST" action="{{ route('submit.login') }}">
            @csrf

            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email address</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="auth-input @error('email') border-red-400 @enderror"
                           placeholder="you@company.com" autocomplete="email">
                </div>
                @error('email')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="password" id="password" name="password" required
                           class="auth-input pr-11 @error('password') border-red-400 @enderror"
                           placeholder="Enter your password" autocomplete="current-password">
                    <button type="button" id="togglePassword" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" aria-label="Toggle password visibility">
                        <i class="fas fa-eye text-sm"></i>
                    </button>
                </div>
                @error('password')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between text-sm">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-700">Forgot password?</a>
            </div>

            <button type="submit" id="loginButton" class="auth-btn mt-2">
                Sign In
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500 lg:hidden">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-700">Create one</a>
        </p>

        <p class="mt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ $globalSettings->company_name ?? 'ReconX' }}. All rights reserved.
        </p>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        if (!passwordInput || !togglePassword) return;
        togglePassword.addEventListener('click', () => {
            const icon = togglePassword.querySelector('i');
            const show = passwordInput.type === 'password';
            passwordInput.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
        });
    });
</script>
@endpush
