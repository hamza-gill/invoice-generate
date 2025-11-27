@extends('layouts.guest.app')

@section('title', 'Forgot Password - ' . $globalSettings->company_name ?? config('app.name'))

@section('content')
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-xl mb-4">
                    <i class="fas fa-key text-blue-600 text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Forgot Password?</h1>
                <p class="text-gray-600 mt-2">No worries, we'll send you reset instructions</p>
            </div>

            <div id="errorMessage" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-600"></p>
            </div>

            <div id="successMessage" class="hidden mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-check-circle text-green-600 mt-0.5 mr-2"></i>
                    <p class="text-sm text-green-600"></p>
                </div>
            </div>

            @include('layouts.errors')

            <form id="forgotPasswordForm" class="space-y-5" action="{{ route('password.email') }}" method="post">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="email" id="email" required name="email"
                               class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter your email">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">We'll send a password reset link to this email</p>
                </div>

                <button type="submit" id="resetButton"
                        class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Send Reset Link
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

    <script src="auth.js"></script>
@endsection
