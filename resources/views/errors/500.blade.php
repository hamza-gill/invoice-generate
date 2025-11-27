<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error | {{($globalSettings->company_name ?? config('app.name'))}}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'slate-950': '#0f172a',
                        'slate-900': '#1e293b',
                        'slate-800': '#334155',
                        primary: '#2563eb',
                        accent: '#8b5cf6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center p-4">

<div class="max-w-md w-full">
    <!-- Error Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-8 text-center shadow-2xl">
        <!-- Icon -->
        <div class="flex justify-center mb-6">
            <div class="bg-accent p-4 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Error Code -->
        <h1 class="text-6xl font-bold text-white mb-4">500</h1>

        <!-- Message -->
        <h2 class="text-2xl font-semibold text-white mb-3">Internal Server Error</h2>
        <p class="text-slate-400 mb-8 leading-relaxed">
            Oops! Something went wrong on our end.<br>
            Our team has been notified and is working to fix it.
        </p>

        <!-- Actions -->
        <div class="space-y-3">
            <button onclick="window.location.reload()"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-medium py-3 px-6 rounded-lg transition-colors duration-200">
                Try Again
            </button>

            <a href="{{ url('/') }}"
               class="block w-full bg-slate-800 hover:bg-slate-700 text-white font-medium py-3 px-6 rounded-lg border border-slate-700 transition-colors duration-200">
                Return Home
            </a>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="text-center mt-6">
        <p class="text-slate-500 text-sm">Error Code: 500 | Internal Server Error</p>
    </div>
</div>

</body>
</html>
