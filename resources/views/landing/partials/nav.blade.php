<header class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 backdrop-blur-lg">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6">
        <a href="{{ route('landing') }}" class="flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg gradient-primary text-white shadow-soft">
                <i class="fas fa-bolt text-sm"></i>
            </div>
            <span class="text-lg font-bold tracking-tight">Inveqi</span>
        </a>
        <nav class="hidden items-center gap-6 lg:gap-8 md:flex">
            <a href="{{ route('features') }}" class="text-sm {{ ($active ?? '') === 'features' ? 'text-gray-900 font-medium' : 'text-gray-500 transition hover:text-gray-900' }}">Features</a>
            <a href="{{ route('pricing') }}" class="text-sm {{ ($active ?? '') === 'pricing' ? 'text-gray-900 font-medium' : 'text-gray-500 transition hover:text-gray-900' }}">Pricing</a>
            <a href="{{ route('docs') }}" class="text-sm {{ ($active ?? '') === 'docs' ? 'text-gray-900 font-medium' : 'text-gray-500 transition hover:text-gray-900' }}">Docs</a>
            <a href="{{ route('contact') }}" class="text-sm {{ ($active ?? '') === 'contact' ? 'text-gray-900 font-medium' : 'text-gray-500 transition hover:text-gray-900' }}">Contact</a>
            <a href="{{ route('login') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Login</a>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('register') }}" class="hidden gradient-primary text-white text-sm font-medium px-4 py-2 rounded-lg shadow-soft hover:opacity-90 sm:inline-flex">Get Started</a>
        </div>
    </div>
</header>
