<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inveqi')</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gradient-primary: linear-gradient(135deg, #3b82f6, #60a5fa);
            --gradient-hero: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
            --shadow-glow: 0 20px 60px -20px rgba(59,130,246,0.35);
            --shadow-card: 0 1px 3px rgba(59,130,246,0.05), 0 10px 30px -10px rgba(59,130,246,0.12);
        }
        .gradient-primary { background-image: var(--gradient-primary); }
        .text-gradient {
            background-image: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .bg-grid {
            background-image:
                linear-gradient(to right, rgba(59,130,246,0.08) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(59,130,246,0.08) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .auth-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 0.9375rem;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .auth-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }
        .auth-btn {
            background-image: var(--gradient-primary);
            color: #fff;
            font-weight: 600;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            width: 100%;
            transition: opacity 0.15s, transform 0.15s;
        }
        .auth-btn:hover { opacity: 0.92; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen antialiased text-gray-900" style="background: var(--gradient-hero);">
    <div class="absolute inset-0 -z-10 bg-grid opacity-40" style="mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%); -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);"></div>
    <div class="absolute -top-32 left-1/2 -z-10 h-96 w-[36rem] -translate-x-1/2 rounded-full bg-blue-500/10 blur-3xl"></div>

    <header class="border-b border-white/60 bg-white/70 backdrop-blur-lg">
        <div class="mx-auto flex h-14 sm:h-16 max-w-6xl items-center justify-between px-4 sm:px-6">
            <a href="{{ route('landing') }}" class="flex items-center gap-2">
                <div class="flex h-8 w-8 sm:h-9 sm:w-9 items-center justify-center rounded-lg gradient-primary text-white shadow-sm">
                    <i class="fas fa-bolt text-xs sm:text-sm"></i>
                </div>
                <span class="text-base sm:text-lg font-bold tracking-tight">Inveqi</span>
            </a>
            <div class="flex items-center gap-2 sm:gap-3 text-sm">
                @hasSection('auth-alt-link')
                    @yield('auth-alt-link')
                @endif
            </div>
        </div>
    </header>

    <main class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-6xl items-center px-4 py-8 sm:py-12">
        <div class="grid w-full items-center gap-8 lg:grid-cols-2 lg:gap-12">
            <div class="hidden lg:block">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/5 px-4 py-1.5 text-xs font-medium text-blue-600">
                    <i class="fas fa-shield-alt"></i>
                    Secure · Multi-tenant invoicing
                </div>
                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight">
                    @yield('auth-heading', 'Invoice management made simple')
                </h1>
                <p class="mt-4 text-lg text-gray-500 leading-relaxed">
                    @yield('auth-subheading', 'Create invoices, send estimates, accept payments, and manage recurring billing — all in one place.')
                </p>
                <ul class="mt-8 space-y-3 text-sm text-gray-600">
                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-blue-500"></i> 10+ professional invoice templates</li>
                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-blue-500"></i> Stripe payments &amp; recurring invoices</li>
                    <li class="flex items-center gap-3"><i class="fas fa-check-circle text-blue-500"></i> Estimates with one-click approval</li>
                </ul>
            </div>

            <div class="w-full max-w-md mx-auto lg:max-w-none lg:ml-auto">
                @yield('content')
            </div>
        </div>
    </main>
    @stack('scripts')
</body>
</html>
