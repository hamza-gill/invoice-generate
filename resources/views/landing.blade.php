@extends('layouts.guest.app')

@section('title', 'Welcome to ' . config('app.name', 'ReconX'))

@section('content')
    <div class="w-full flex flex-col min-h-screen bg-gradient-to-b from-blue-50 via-white to-gray-100">

        {{-- 🔹 Header --}}
        <header class="w-full bg-white/80 backdrop-blur-lg shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exchange-alt text-blue-600 text-2xl"></i>
                    <h1 class="text-xl font-bold text-gray-800">{{ config('app.name', 'ReconX') }}</h1>
                </div>
                <div>
                    <a href="{{ route('login') }}"
                       class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </a>
                </div>
            </div>
        </header>

        {{-- 🔹 Hero Section --}}
        <section class="flex flex-col md:flex-row items-center justify-between max-w-7xl mx-auto px-6 py-16 md:py-24">
            <div class="flex-1 text-center md:text-left space-y-6">
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight">
                    Simplify Your <span class="text-blue-600">Billing</span> & <span class="text-blue-600">Invoicing</span>
                </h2>
                <p class="text-gray-600 text-lg max-w-lg mx-auto md:mx-0">
                    {{ config('app.name', 'ReconX') }} helps you manage invoices, subscriptions, and payments effortlessly — all in one secure dashboard.
                </p>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a href="#pricing"
                       class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        View Plans
                    </a>
                    <a href="{{ route('login') }}"
                       class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg font-semibold hover:bg-gray-300 transition">
                        Try Demo
                    </a>
                </div>
            </div>
            <div class="flex-1 mt-10 md:mt-0 flex justify-center">
                <img src="https://cdn.dribbble.com/users/1162077/screenshots/15627539/media/8d23865d859ba74cb8f9e458b2f493f8.png"
                     alt="Dashboard Preview"
                     class="w-full max-w-lg rounded-xl shadow-2xl animate-float">
            </div>
        </section>

        {{-- 🔹 Features Section --}}
        <section class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h3 class="text-3xl font-bold text-gray-900 mb-10">Powerful Features</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="bg-gray-50 rounded-xl shadow-sm p-8 hover:shadow-lg transition">
                        <i class="fas fa-file-invoice-dollar text-blue-600 text-3xl mb-4"></i>
                        <h4 class="font-semibold text-lg mb-2">Smart Invoicing</h4>
                        <p class="text-gray-600">Generate professional invoices instantly and track payments in real-time.</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl shadow-sm p-8 hover:shadow-lg transition">
                        <i class="fas fa-users text-blue-600 text-3xl mb-4"></i>
                        <h4 class="font-semibold text-lg mb-2">Client Management</h4>
                        <p class="text-gray-600">Keep your clients organized and send reminders automatically.</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl shadow-sm p-8 hover:shadow-lg transition">
                        <i class="fas fa-lock text-blue-600 text-3xl mb-4"></i>
                        <h4 class="font-semibold text-lg mb-2">Secure & Reliable</h4>
                        <p class="text-gray-600">Your data is encrypted and protected by industry-grade security.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 🔹 Extended Info Section --}}
        <section class="bg-gradient-to-b from-blue-50 to-white py-20">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">Why Choose {{ config('app.name', 'ReconX') }}</h3>
                <p class="text-gray-600 max-w-3xl mx-auto mb-12">
                    {{ config('app.name', 'ReconX') }} isn’t just another invoicing app — it’s your all-in-one solution for subscription billing, client follow-ups, and customizable invoices.
                    Automate your entire financial workflow and focus on growing your business.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg transition">
                        <i class="fas fa-clock text-blue-600 text-3xl mb-4"></i>
                        <h4 class="font-semibold text-lg mb-2">Automated Subscriptions</h4>
                        <p class="text-gray-600">
                            Manage recurring billing effortlessly. Send invoices and reminders every month — automatically.
                        </p>
                    </div>
                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg transition">
                        <i class="fas fa-paint-brush text-blue-600 text-3xl mb-4"></i>
                        <h4 class="font-semibold text-lg mb-2">Customizable Invoices</h4>
                        <p class="text-gray-600">
                            Personalize invoice templates with your logo, color scheme, and notes. Make every invoice on-brand.
                        </p>
                    </div>
                    <div class="bg-white rounded-xl p-8 shadow-md hover:shadow-lg transition">
                        <i class="fas fa-envelope-open-text text-blue-600 text-3xl mb-4"></i>
                        <h4 class="font-semibold text-lg mb-2">Follow-up Emails</h4>
                        <p class="text-gray-600">
                            Automatically notify clients before and after due dates. Improve your cash flow with timely reminders.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- 🔹 Payments & Gateways Section --}}
        <section class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">Payments & Integrations</h3>
                <p class="text-gray-600 max-w-2xl mx-auto mb-8">
                    Accept payments from your clients with the gateway you trust. ReconX supports multiple gateways out-of-the-box,
                    and we can add custom integrations on request.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    @php
                        $gateways = [
                            ['name'=>'Stripe','img'=>'https://cdn.iconscout.com/icon/free/png-256/stripe-226579.png','desc'=>'Card payments, subscriptions, webhooks, and PCI-ready checkout.'],
                            ['name'=>'PayPal','img'=>'https://cdn.iconscout.com/icon/free/png-256/paypal-226455.png','desc'=>'PayPal checkout for customers who prefer PayPal wallets.'],
                            ['name'=>'Authorize.net','img'=>'https://cdn.iconscout.com/icon/free/png-256/authorize-2752191-2284982.png','desc'=>'Reliable gateway for US-based merchants and payment processors.'],
                            ['name'=>'WePay','img'=>'https://cdn.iconscout.com/icon/free/png-256/wepay-282753.png','desc'=>'Payments for platforms and marketplaces, simple onboarding.']
                        ];
                    @endphp
                    @foreach($gateways as $gw)
                        <div class="p-6 border rounded-xl hover:shadow-lg transition flex flex-col items-center">
                            <img src="{{ $gw['img'] }}" alt="{{ $gw['name'] }}" class="h-12 mb-4 animate-logo">
                            <h4 class="font-semibold mb-2">{{ $gw['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $gw['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="max-w-4xl mx-auto text-left bg-gray-50 p-6 rounded-xl shadow-sm">
                    <h4 class="font-semibold text-lg mb-3">Payment Features</h4>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-gray-700">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> PCI-compliant checkout options</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Automatic recurring billing & retries</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Webhooks for real-time payment updates</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Custom payment gateway integrations on demand</li>
                    </ul>
                    <div class="mt-6 flex flex-col md:flex-row items-center md:justify-between gap-4">
                        <p class="text-sm text-gray-600">Need a gateway that's not listed? Our team can integrate any provider you require — reach out to support.</p>
                        <a href="{{ route('login') }}" class="inline-block bg-blue-600 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                            Connect a Gateway
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- 🔹 Dynamic Invoice Animation Section --}}
        <section class="bg-gradient-to-br from-blue-50 to-white py-20 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">Dynamic & Customizable Invoicing</h3>
                <p class="text-gray-600 max-w-2xl mx-auto mb-12">
                    Create beautiful invoices that reflect your brand. Add your logo, choose colors, and automate follow-ups —
                    all with live previews and real-time editing.
                </p>
                <div class="relative flex justify-center">
                    <div class="bg-white shadow-2xl rounded-xl w-full max-w-2xl p-8 transform hover:scale-105 transition duration-700 animate-float">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-lg text-gray-800">Invoice #INV-1023</h4>
                            <span class="text-sm text-gray-500">Due: 12 Nov 2025</span>
                        </div>
                        <hr class="mb-4">
                        <div class="space-y-2 text-left text-gray-700">
                            <p><strong>Client:</strong> Acme Corp.</p>
                            <p><strong>Service:</strong> Website Design</p>
                            <p><strong>Amount:</strong> <span class="text-blue-600 font-bold">$1,250</span></p>
                        </div>
                        <div class="mt-6 flex justify-between">
                            <span class="text-sm text-gray-400">Auto-sent every month</span>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                                Send Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Animated background shapes --}}
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20">
                <div class="w-72 h-72 bg-blue-200 rounded-full absolute -top-10 -left-10 animate-pulse"></div>
                <div class="w-60 h-60 bg-indigo-200 rounded-full absolute bottom-0 right-0 animate-bounce"></div>
            </div>
        </section>

        {{-- 🔹 Pricing Section --}}
        <section id="pricing" class="bg-gradient-to-b from-gray-50 to-white py-20">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Choose Your Plan</h3>
                <p class="text-gray-600 mb-12 text-lg max-w-2xl mx-auto">
                    Flexible pricing for every stage of your business. Check the invoice limits for each plan below.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($plans as $plan)
                        <div class="relative bg-white rounded-3xl shadow-md p-8 transition-transform transform hover:scale-105 hover:shadow-xl border border-gray-100">

                            {{-- Badge / Limit --}}
                            <div class="absolute top-0 right-0 px-3 py-1 rounded-bl-lg text-xs font-semibold text-white
                {{ $plan->limit_count === 0 ? 'bg-green-500' : 'bg-gray-500' }}">
                                @if ($plan->limit_count === 0)
                                    Unlimited
                                @else
                                    {{ $plan->limit_count }} Invoices
                                @endif
                            </div>

                            <h4 class="text-2xl font-bold mb-2 text-gray-800">{{ $plan->name }}</h4>
                            <p class="text-gray-500 mb-6">{{ $plan->description }}</p>
                            <div class="text-4xl font-extrabold text-blue-600 mb-6">${{ $plan->price }}<span class="text-lg text-gray-500">/mo</span></div>

                            <ul class="text-gray-700 space-y-3 mb-8">
                                {{-- Limit count --}}
                                <li class="flex items-center">
                                    <i class="fas fa-file-invoice text-indigo-500 mr-3"></i>
                                    @if ($plan->limit_count === 0)
                                        Unlimited Invoices
                                    @else
                                        {{ $plan->limit_count }} Invoices / Month
                                    @endif
                                </li>

                                {{-- JSON-decoded features --}}
                                @if(!empty($plan->features))
                                    @foreach(json_decode($plan->features) as $feature)
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-500 mr-3"></i> {{ $feature }}
                                        </li>
                                    @endforeach
                                @endif
                            </ul>

                            <a href="{{ route('login') }}"
                               class="block w-full py-3 rounded-lg font-semibold text-white
              {{ $plan->slug == 'pro' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-800 hover:bg-gray-900' }}
              transition">
                                {{ $plan->slug == 'pro' ? 'Choose Plan' : 'Get Started' }}
                            </a>
                        </div>
                    @endforeach


                </div>
            </div>
        </section>

        {{-- 🔹 Support Section --}}
        <section class="bg-blue-600 text-white py-16">
            <div class="max-w-6xl mx-auto px-6 text-center">
                <h3 class="text-3xl font-bold mb-6">We’re Here to Help</h3>
                <p class="text-blue-100 max-w-3xl mx-auto mb-10">
                    Our team provides full support for integration, customization, and payment gateways.
                    Accept payments via <strong>Stripe</strong>, <strong>PayPal</strong>, <strong>Authorize.net</strong>, and <strong>WePay</strong> —
                    or request any other gateway you need.
                </p>
                <a href="{{ route('login') }}"
                   class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                    Contact Support
                </a>
            </div>
        </section>

        {{-- 🔹 Footer --}}
        @include('layouts.guest.footer')

    </div>

    {{-- 🔹 Floating Animation Keyframe & logo animation --}}
    <style>
        @keyframes float {
            0%,100%{transform:translateY(0px);}
            50%{transform:translateY(-10px);}
        }
        .animate-float { animation: float 3s ease-in-out infinite; }

        @keyframes logoPulse {
            0%{transform:scale(1);opacity:0.95;}
            50%{transform:scale(1.06);opacity:1;}
            100%{transform:scale(1);opacity:0.95;}
        }
        .animate-logo { animation: logoPulse 4s ease-in-out infinite; }
    </style>

@endsection
