<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | {{($globalSettings->company_name ?? config('app.name'))}}</title>
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
        <div class="bg-blue-100 text-blue-600 p-4 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9.172 16.172a4 4 0 015.656 0m-5.656 0a4 4 0 010-5.656m5.656 5.656a4 4 0 000-5.656M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    <!-- Error Title -->
    <h1 class="text-6xl font-extrabold text-gray-900 mb-2">404</h1>
    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Page Not Found</h2>

    <!-- Message -->
    <p class="text-gray-500 mb-8 leading-relaxed">
        Sorry, the page you’re looking for doesn’t exist or may have been moved.<br>
        Please check the URL or return to the homepage.
    </p>

    <!-- Buttons -->
    <div class="space-y-3">
        <button onclick="history.back()"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 rounded-lg transition-colors duration-200">
            Go Back
        </button>

        <a href="{{ url('/') }}"
           class="block w-full bg-primary hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition-colors duration-200">
            Return Home
        </a>
    </div>

    <!-- Footer Note -->
    <div class="mt-8 text-sm text-gray-400">
        Error Code: 404 | Page Not Found
    </div>
</div>

</body>
</html>
