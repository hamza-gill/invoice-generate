<section class="border-t border-gray-200/60 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Free tools</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">Try more free invoicing tools</h2>
            <p class="mt-4 text-gray-500">Quick, useful calculators and generators for your everyday billing.</p>
        </div>
        <div class="mt-14 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($related as $tool)
            <a href="{{ route('tools.' . $tool['slug']) }}" class="group rounded-2xl border border-gray-200 bg-white p-6 shadow-card transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-glow">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl gradient-primary text-white">
                    <i class="fas fa-calculator text-sm"></i>
                </div>
                <div class="mt-4 text-base font-semibold text-gray-900 group-hover:text-blue-600">{{ $tool['title'] }}</div>
                <p class="mt-2 text-sm text-gray-500">{{ $tool['desc'] }}</p>
                <div class="mt-4 flex items-center gap-1 text-sm font-medium text-blue-600">
                    Open tool <i class="fas fa-arrow-right text-xs transition group-hover:translate-x-1"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
