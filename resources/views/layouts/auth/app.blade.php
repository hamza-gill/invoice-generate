<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($globalSettings->company_name ?? config('app.name')))</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        sidebar: '#1e293b',
                        'sidebar-hover': '#334155'
                    }
                }
            }
        }
    </script>

    <style>
        /* Only ONE element should own vertical scrolling. Applying height:100%
           + overflow-y:auto to BOTH html and body creates two independent
           scroll containers once content overflows — html scrolls its box,
           and body separately scrolls inside it, giving two scrollbars.
           Body is the sole scroll owner here; html just gets smooth-scroll
           behavior without becoming its own scroll container. */
        html {
            scroll-behavior: smooth;
        }
        body {
            min-height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    @include('layouts.auth.sidebar')

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        {{-- Navbar --}}
        @if(!isset($hideNavbar) || !$hideNavbar)
            @include('layouts.auth.navbar')
        @endif

        {{-- Page Content --}}
        <main class="p-8">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('layouts.auth.footer')
    </div>
</div>
</body>
</html>
