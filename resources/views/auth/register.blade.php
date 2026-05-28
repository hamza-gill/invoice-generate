@extends('layouts.guest.app')

@section('title', 'Register')

@section('content')
<div class="w-full max-w-lg bg-white rounded-2xl shadow-xl p-8">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Create your account</h2>
    <p class="text-gray-500 text-sm mb-6">Start your 14-day free trial. No credit card required.</p>

    @include('layouts.errors')

    <form class="space-y-4" action="{{ route('submit.register') }}" method="post">
        @csrf

        <input type="text" name="company_name" placeholder="Company / Business Name*" value="{{ old('company_name') }}"
               class="w-full p-3 border rounded-lg @error('company_name') border-red-500 @enderror" required />

        <div class="grid grid-cols-2 gap-4">
            <input type="text" name="first_name" placeholder="First Name*" value="{{ old('first_name') }}"
                   class="w-full p-3 border rounded-lg" required />
            <input type="text" name="last_name" placeholder="Last Name*" value="{{ old('last_name') }}"
                   class="w-full p-3 border rounded-lg" required />
        </div>

        <input type="email" name="email" placeholder="Email*" value="{{ old('email') }}"
               class="w-full p-3 border rounded-lg" required />

        <input type="text" name="phone" placeholder="Phone*" value="{{ old('phone') }}"
               class="w-full p-3 border rounded-lg" required />

        <input type="password" name="password" placeholder="Password*" 
               class="w-full p-3 border rounded-lg" required />

        <input type="password" name="password_confirmation" placeholder="Confirm Password*"
               class="w-full p-3 border rounded-lg" required />

        <label class="flex items-start gap-2 text-sm text-gray-600">
            <input type="checkbox" name="terms" class="mt-1" required />
            <span>I agree to the Terms of Service</span>
        </label>
        <label class="flex items-start gap-2 text-sm text-gray-600">
            <input type="checkbox" name="privacy" class="mt-1" required />
            <span>I agree to the Privacy Policy</span>
        </label>
        <label class="flex items-start gap-2 text-sm text-gray-600">
            <input type="checkbox" name="sharing" class="mt-1" required />
            <span>I agree to data processing for account setup</span>
        </label>

        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold">
            Create Account & Start Trial
        </button>

        <p class="text-center text-sm text-gray-500">
            Already have an account? <a href="{{ route('login') }}" class="text-orange-500 font-medium">Login</a>
        </p>
    </form>
</div>
@endsection
