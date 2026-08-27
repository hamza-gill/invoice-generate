@extends('layouts.auth.app')

@section('title', 'Add Customer - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="w-full max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Add New Customer</h1>
                <p class="text-gray-500 mt-1">Fill in the customer details below to add them to your system.</p>
            </div>
            <a href="{{ route('customers.index') }}"
               class="inline-flex items-center px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <form action="{{ route('customers.store') }}" method="POST"
              class="bg-white p-8 rounded-2xl shadow-md border border-gray-100 transition-all hover:shadow-lg">
            @csrf

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- First Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-1 text-blue-600"></i> First Name
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="First name" required>
                </div>

                {{-- Last Name --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user mr-1 text-blue-600"></i> Last Name
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="Last name" required>
                </div>

                {{-- Company Name --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building mr-1 text-blue-600"></i> Company Name
                    </label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="Company or organization name">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-1 text-blue-600"></i> Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="customer@example.com" required>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone mr-1 text-blue-600"></i> Phone Number
                    </label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="+1 555 123 4567">
                </div>

                {{-- Address --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-1 text-blue-600"></i> Address
                    </label>
                    <input type="text"  id="address" name="address" value="{{ old('address') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="Street address, P.O. Box, etc.">
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-city mr-1 text-blue-600"></i> City
                    </label>
                    <input type="text" name="city" value="{{ old('city') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="City">
                </div>

                {{-- State --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map mr-1 text-blue-600"></i> State
                    </label>
                    <input type="text" name="state" value="{{ old('state') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="State or province">
                </div>

                {{-- Postal Code --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-mail-bulk mr-1 text-blue-600"></i> Postal Code
                    </label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg
                      focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                           placeholder="ZIP / Postal code">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-flag mr-1 text-blue-600"></i> Country
                    </label>
                    <select name="country" id="country"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white
                       focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                        <option value="">Select country</option>
                        @include('customers.partials.countries')
                    </select>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('customers.index') }}"
                   class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition">
                    <i class="fas fa-save mr-2"></i> Save Customer
                </button>
            </div>
        </form>
    </div>

    <style>
        .select2-container--default .select2-selection--single { height:42px !important; border:1px solid #d1d5db !important; border-radius:0.5rem !important; display:flex; align-items:center; }
        .select2-container .select2-selection--single .select2-selection__rendered { line-height:42px !important; padding-left:1rem !important; }
        .select2-selection__arrow { top:8px !important; right:8px !important; }
        .select2-container { width:100% !important; }
        .select2-results__options { max-height:300px !important; }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet" />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery.fn.select2) {
                $('#country').select2({
                    placeholder: 'Search country',
                    allowClear: true
                });
            }
        });
    </script>

    <script>
        // Google Places Autocomplete
        function initAutocomplete() {

            function attachAutocomplete(inputId) {
                const input = document.getElementById(inputId);
                if (!input) return;

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['address'],
                    componentRestrictions: { country: 'us' },
                    fields: ['formatted_address']
                });

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (place && place.formatted_address) {
                        input.value = place.formatted_address;
                    }
                });
            }

            // Customer Address
            attachAutocomplete('address');
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{$globalSettings->google_places_key}}&libraries=places&callback=initAutocomplete" async defer></script>

@endsection
