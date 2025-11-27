<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Session Expired | {{($globalSettings->company_name ?? config('app.name'))}}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        sidebar: '#1e293b',
                        'sidebar-hover': '#334155'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans">

<div class="max-w-lg w-full bg-white rounded-2xl shadow-lg border border-gray-200 text-center p-10">
    <!-- Icon -->
    <div class="flex justify-center mb-6">
        <div class="bg-yellow-100 text-yellow-600 p-4 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>

    <!-- Error Code -->
    <h1 class="text-6xl font-extrabold text-gray-900 mb-2">419</h1>
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Session Expired</h2>

    <!-- Message -->
    <p class="text-gray-500 mb-8 leading-relaxed">
        Your session has expired for security reasons.<br>
        Please refresh the page or log in again to continue.
    </p>

    <!-- Buttons -->
    <div class="space-y-3">
        <button onclick="window.location.reload()"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-lg transition-colors duration-200">
            Refresh Page
        </button>

        <a href="{{ route('login') }}"
           class="block w-full bg-primary hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors duration-200">
            Sign In Again
        </a>

        <a href="{{ url('/') }}"
           class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-3 rounded-lg transition-colors duration-200 border border-gray-300">
            Return Home
        </a>
    </div>

    <!-- Footer Note -->
    <div class="mt-8 text-sm text-gray-400">
        Error Code: 419 | Authentication Timeout
    </div>
</div>

</body>
</html>
