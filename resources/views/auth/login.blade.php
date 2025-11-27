@extends('layouts.guest.app')
@section('title', 'Login - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-xl mb-4">
                    <i class="fas fa-exchange-alt text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Welcome to {{ $globalSettings->company_name ?? config('app.name') }}</h1>
                <p class="text-gray-600 mt-2">Sign in to your account to continue</p>
            </div>

            {{-- ✅ Error Alert Section --}}
            @if ($errors->any())
                <div id="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ✅ Login Form --}}
            <form id="loginForm" class="space-y-5" method="POST" action="{{ route('submit.login') }}">
                @csrf

                {{-- Email Field --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                   @error('email') border-red-500 @enderror"
                            placeholder="Enter your email"
                        >
                    </div>
                    @error('email')
                    <p class="text-sm text-red-500 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-lg
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                   @error('password') border-red-500 @enderror"
                            placeholder="Enter your password"
                        >
                        <button type="button" id="togglePassword"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                    <p class="text-sm text-red-500 mt-1 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me + Forgot Password --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                            {{ old('remember') ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Forgot password?
                    </a>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="loginButton"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700
                               transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Sign In
                </button>
            </form>
        </div>

        {{-- ✅ Dynamic Footer --}}
        @include('layouts.guest.footer')
    </div>

    {{-- ✅ Password Visibility Toggle Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            togglePassword.addEventListener('click', () => {
                const icon = togglePassword.querySelector('i');
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
@endsection
