<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden | {{($globalSettings->company_name ?? config('app.name'))}}</title>
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
        <div class="bg-red-100 text-red-600 p-4 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <!-- Error Title -->
    <h1 class="text-6xl font-extrabold text-gray-900 mb-2">403</h1>
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Access Forbidden</h2>

    <!-- Message -->
    <p class="text-gray-500 mb-8 leading-relaxed">
        You don’t have permission to view this page.<br>
        Please contact your administrator if you believe this is an error.
    </p>

    <!-- Buttons -->
    <div class="space-y-3">
        <button onclick="history.back()"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-lg transition-colors duration-200">
            Go Back
        </button>

        <a href="{{ route('login') }}"
           class="block w-full bg-primary hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors duration-200">
            Sign In
        </a>

        <a href="{{ url('/') }}"
           class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-lg transition-colors duration-200 border border-gray-300">
            Return Home
        </a>
    </div>

    <!-- Footer Note -->
    <div class="mt-8 text-sm text-gray-400">
        Error Code: 403 | Access Forbidden
    </div>
</div>

</body>
</html>
