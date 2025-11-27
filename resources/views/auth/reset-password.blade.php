@extends('layouts.guest.app')

@section('title', 'Reset Password - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-xl mb-4">
                    <i class="fas fa-lock text-blue-600 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
                <p class="text-gray-600 mt-2">Enter your new password below</p>
            </div>

            <div id="errorMessage" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600"></p>
            </div>

            <div id="successMessage" class="hidden mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-600"></p>
            </div>

            <form id="resetPasswordForm" class="space-y-5" action="{{ route('password.update') }}" method="post">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" id="password" required name="password"
                               class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter new password">
                        <button type="button" id="togglePassword"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                        <p class="text-sm text-red-500 mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters long</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="password" id="confirmPassword" required name="password_confirmation"
                               class="w-full pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Confirm new password">
                        <button type="button" id="toggleConfirmPassword"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="resetButton"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Reset Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to login
                </a>
            </div>
        </div>

        {{-- Include Dynamic Footer --}}
        @include('layouts.guest.footer')
    </div>

    {{-- ✅ Functional JS for toggling password visibility --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

            // Toggle main password
            togglePassword.addEventListener('click', () => {
                const icon = togglePassword.querySelector('i');
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });

            // Toggle confirm password
            toggleConfirmPassword.addEventListener('click', () => {
                const icon = toggleConfirmPassword.querySelector('i');
                const isHidden = confirmPasswordInput.type === 'password';
                confirmPasswordInput.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });
    </script>
@endsection
