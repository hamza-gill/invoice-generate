<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login - Recon')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <style>
        :root{
            --bloom-1: #4f46e5;
            --bloom-2: #7c3aed;
            --bloom-3: #a855f7;
            --bloom-bg: linear-gradient(135deg, rgba(79,70,229,0.08) 0%, rgba(124,58,237,0.08) 45%, rgba(168,85,247,0.08) 100%);
            --bloom-header: linear-gradient(135deg, #4f46e5 0%, #7c3aed 45%, #a855f7 100%);
        }
        .bloom-bg{ background: var(--bloom-bg); }
        .bloom-grid{
            background-image:
                radial-gradient(circle at 20% 20%, rgba(79,70,229,0.12), transparent 45%),
                radial-gradient(circle at 80% 10%, rgba(124,58,237,0.10), transparent 40%),
                radial-gradient(circle at 30% 90%, rgba(168,85,247,0.10), transparent 50%),
                linear-gradient(to right, rgba(79,70,229,0.06) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(79,70,229,0.06) 1px, transparent 1px);
            background-size: auto, auto, auto, 44px 44px, 44px 44px;
        }
    </style>
    @yield('theme-styles')
</head>
<body class="min-h-screen bloom-bg bloom-grid text-slate-900 @yield('body-class')">
    <div class="mx-auto w-full max-w-6xl px-4 py-10">
        @yield('content')
    </div>
</body>

</html>
