<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Platform Settings - Inveqi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-white shadow-sm">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-800">Inveqi Platform</h1>
                <p class="text-xs text-gray-500">Signed in as {{ auth('platform')->user()->name }}</p>
            </div>
            <form method="POST" action="{{ route('platform.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Logout</button>
            </form>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto p-6">
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc pl-5 space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Stripe (Platform) Keys</h2>
            <p class="text-sm text-gray-500 mb-6">
                These keys belong to the platform's own Stripe account and are used for subscription
                checkout. Keys saved here are encrypted and take priority over <span class="font-mono">.env</span> values.
                Leave a field blank to keep the currently stored value.
            </p>

            @if($hasSecretKey)
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    <i class="fas fa-check mr-1"></i> A Stripe secret key is configured and ready to use.
                </div>
            @else
                <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg text-sm">
                    No Stripe secret key configured. Subscription checkout will fail until a key is saved below.
                </div>
            @endif

            <form method="POST" action="{{ route('platform.settings.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Public Key</label>
                    <input type="text" name="stripe_publishable_key"
                           placeholder="pk_live_..."
                           class="w-full border border-gray-300 rounded-lg p-2.5 font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">{{ $sources['stripe_publishable_key'] }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Secret Key</label>
                    <input type="password" name="stripe_secret_key"
                           placeholder="sk_live_..."
                           class="w-full border border-gray-300 rounded-lg p-2.5 font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">{{ $sources['stripe_secret_key'] }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stripe Webhook Secret</label>
                    <input type="password" name="stripe_webhook_secret"
                           placeholder="whsec_..."
                           class="w-full border border-gray-300 rounded-lg p-2.5 font-mono text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">{{ $sources['stripe_webhook_secret'] }}</p>
                </div>

                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition">
                    Save Platform Settings
                </button>
            </form>
        </div>
    </main>
</body>
</html>
