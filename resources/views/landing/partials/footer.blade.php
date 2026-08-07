<footer class="border-t border-gray-200/60 py-12">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-8 md:grid-cols-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg gradient-primary text-white">
                        <i class="fas fa-bolt text-sm"></i>
                    </div>
                    <span class="text-lg font-bold tracking-tight">Inveqi</span>
                </div>
                <p class="mt-3 max-w-xs text-sm text-gray-500">Invoice management built for every business.</p>
            </div>
            <div>
                <div class="text-sm font-semibold">Product</div>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('features') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Features</a></li>
                    <li><a href="{{ route('pricing') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Pricing</a></li>
                    <li><a href="{{ route('integrations') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Integrations</a></li>
                    <li><a href="{{ route('changelog') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Changelog</a></li>
                </ul>
            </div>
            <div>
                <div class="text-sm font-semibold">Company</div>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('about') }}" class="text-sm text-gray-500 transition hover:text-gray-900">About</a></li>
                    <li><a href="{{ route('blog') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Blog</a></li>
                    <li><a href="{{ route('careers') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Careers</a></li>
                    <li><a href="{{ route('contact') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Contact</a></li>
                </ul>
            </div>
            <div>
                <div class="text-sm font-semibold">Resources</div>
                <ul class="mt-3 space-y-2">
                    <li><a href="{{ route('docs') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Docs</a></li>
                    <li><a href="{{ route('help-center') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Help center</a></li>
                    <li><a href="{{ route('status') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Status</a></li>
                    <li><a href="{{ route('api') }}" class="text-sm text-gray-500 transition hover:text-gray-900">API</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-10 border-t border-gray-200/60 pt-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} Inveqi. All rights reserved.
            <span class="mx-2">·</span>
            <a href="{{ route('terms') }}" class="hover:text-gray-900 hover:underline">Terms of Service</a>
            <span class="mx-2">·</span>
            <a href="{{ route('privacy') }}" class="hover:text-gray-900 hover:underline">Privacy Policy</a>
        </div>
    </div>
</footer>
