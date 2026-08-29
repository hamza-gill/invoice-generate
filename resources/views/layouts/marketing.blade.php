<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Inveqi - Invoice Management Software')</title>

    <meta name="description" content="@yield('meta_description', 'Inveqi is invoice management software with a drag-and-drop builder, recurring invoices, estimates & quotes, and 10+ professional templates. Create, send and track invoices online and get paid faster with Stripe. Start your free 14-day trial today.')">
    <meta name="keywords" content="@yield('meta_keywords', 'invoice software, free invoice generator, online invoicing, create invoice online, invoice maker, invoice templates, recurring invoices, estimates and quotes, billing software, invoice management, invoice builder, small business invoicing, Stripe invoicing, PDF invoices, payment reminders')">
    <meta name="author" content="Inveqi">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="googlebot" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <meta property="og:type" content="@hasSection('og_type')@yield('og_type')@else website @endif">
    <meta property="og:site_name" content="Inveqi">
    <meta property="og:title" content="@hasSection('og_title')@yield('og_title')@else@yield('title')@endif">
    <meta property="og:description" content="@hasSection('og_description')@yield('og_description')@else@yield('meta_description')@endif">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/hero-invoice.jpg') }}">
    <meta property="og:locale" content="en_US">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@hasSection('og_title')@yield('og_title')@else@yield('title')@endif">
    <meta name="twitter:description" content="@hasSection('og_description')@yield('og_description')@else@yield('meta_description')@endif">
    <meta name="twitter:image" content="{{ asset('images/hero-invoice.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#eef2ff', 100:'#e0e7ff', 200:'#c7d2fe', 300:'#a5b4fc', 400:'#818cf8', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8' },
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --gradient-primary: linear-gradient(135deg, #3b82f6, #60a5fa);
            --gradient-hero: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);
            --shadow-soft: 0 4px 20px -4px rgba(59,130,246,0.15);
            --shadow-glow: 0 20px 60px -20px rgba(59,130,246,0.35);
            --shadow-card: 0 1px 3px rgba(59,130,246,0.05), 0 10px 30px -10px rgba(59,130,246,0.1);
        }
        @keyframes fade-in-up { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        @keyframes float-anim { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-12px); } }
        @keyframes pulse-ring-anim { 0% { transform:scale(0.9); opacity:0.8; } 100% { transform:scale(1.6); opacity:0; } }
        .animate-fade-up { animation: fade-in-up 0.7s ease-out both; }
        .animate-float { animation: float-anim 6s ease-in-out infinite; }
        .animate-pulse-ring { animation: pulse-ring-anim 2s ease-out infinite; }
        .bg-grid {
            background-image: linear-gradient(to right, rgba(59,130,246,0.08) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(59,130,246,0.08) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .gradient-primary { background-image: var(--gradient-primary); }
        .text-gradient { background-image: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .shadow-glow { box-shadow: var(--shadow-glow); }
        .shadow-soft { box-shadow: var(--shadow-soft); }
        .shadow-card { box-shadow: var(--shadow-card); }
        .feature-card:hover .feature-icon { background-image: var(--gradient-primary); color: white; }
        .feature-card:hover .bottom-glow { opacity: 1; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('jsonld')
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@graph": [
            {
                "@@type": "Organization",
                "@@id": "{{ url('/') }}#organization",
                "name": "Inveqi",
                "url": "{{ url('/') }}",
                "logo": "{{ asset('images/hero-invoice.jpg') }}",
                "sameAs": []
            },
            {
                "@@type": "WebSite",
                "@@id": "{{ url('/') }}#website",
                "url": "{{ url('/') }}",
                "name": "Inveqi - Invoice Management Software",
                "description": "Create, send and track professional invoices, estimates and recurring billing online.",
                "publisher": { "@@id": "{{ url('/') }}#organization" }
            }
        ]
    }
    </script>
</head>
<body class="bg-white text-gray-900 min-h-screen antialiased">

    @yield('content')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileBtn = document.getElementById('mobileMenuBtn');
            const mobileMenu = document.getElementById('mobileMenu');
            const menuOpen = document.getElementById('menuIconOpen');
            const menuClose = document.getElementById('menuIconClose');
            if (mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    menuOpen.classList.toggle('hidden');
                    menuClose.classList.toggle('hidden');
                });
            }

            const scrollBtn = document.getElementById('scrollTopBtn');
            if (scrollBtn) {
                window.addEventListener('scroll', function() {
                    scrollBtn.classList.toggle('hidden', window.scrollY < 600);
                });
                scrollBtn.addEventListener('click', function() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            document.querySelectorAll('.faq-toggle').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const content = this.nextElementSibling;
                    const arrow = this.querySelector('.faq-arrow');
                    content.classList.toggle('hidden');
                    arrow.classList.toggle('rotate-180');
                });
            });
        });
    </script>
</body>
</html>
