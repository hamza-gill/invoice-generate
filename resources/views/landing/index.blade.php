@extends('layouts.marketing')

@section('title', 'ReconX — Invoicing, Recurring Billing & Estimates Software')

@section('content')
{{-- Nav --}}
<header class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 backdrop-blur-lg">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-6">
        <a href="{{ route('landing') }}" class="flex items-center gap-2">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg gradient-primary text-white shadow-soft">
                <i class="fas fa-bolt text-sm"></i>
            </div>
            <span class="text-lg font-bold tracking-tight">ReconX</span>
        </a>
        <nav class="hidden items-center gap-8 md:flex">
            <a href="#features" class="text-sm text-gray-500 transition hover:text-gray-900">Features</a>
            <a href="#advanced" class="text-sm text-gray-500 transition hover:text-gray-900">Invoicing</a>
            <a href="#dashboard" class="text-sm text-gray-500 transition hover:text-gray-900">Product</a>
            <a href="#pricing" class="text-sm text-gray-500 transition hover:text-gray-900">Pricing</a>
            <a href="#faq" class="text-sm text-gray-500 transition hover:text-gray-900">FAQ</a>
            <a href="{{ route('login') }}" class="text-sm text-gray-500 transition hover:text-gray-900">Login</a>
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ route('register') }}" class="hidden gradient-primary text-white text-sm font-medium px-4 py-2 rounded-lg shadow-soft hover:opacity-90 md:inline-flex">Get Started</a>
            <button id="mobileMenuBtn" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 md:hidden">
                <i id="menuIconOpen" class="fas fa-bars text-sm"></i>
                <i id="menuIconClose" class="fas fa-times text-sm hidden"></i>
            </button>
        </div>
    </div>
    <div id="mobileMenu" class="hidden border-t border-gray-200/60 bg-white md:hidden">
        <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-6 py-4">
            <a href="#features" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">Features</a>
            <a href="#advanced" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">Invoicing</a>
            <a href="#dashboard" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">Product</a>
            <a href="#pricing" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">Pricing</a>
            <a href="#faq" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">FAQ</a>
            <a href="{{ route('login') }}" class="rounded-lg px-3 py-2 text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900">Login</a>
            <a href="{{ route('register') }}" class="mt-2 gradient-primary text-white text-center px-4 py-2 rounded-lg text-sm font-medium">Get Started</a>
        </nav>
    </div>
</header>

{{-- Hero --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10" style="background: var(--gradient-hero)"></div>
    <div class="absolute inset-0 -z-10 bg-grid opacity-40" style="mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%); -webkit-mask-image: radial-gradient(ellipse at center, black 30%, transparent 70%);"></div>
    <div class="absolute -top-40 left-1/2 -z-10 h-[500px] w-[800px] -translate-x-1/2 rounded-full bg-blue-500/10 blur-3xl"></div>
    <div class="mx-auto max-w-7xl px-6 pb-20 pt-20 md:pt-28">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div class="animate-fade-up">
                <div class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/5 px-4 py-1.5 text-xs font-medium text-blue-600">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="absolute inline-flex h-full w-full rounded-full bg-blue-500 animate-pulse-ring"></span>
                        <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                    </span>
                    New · Multi-tenant SaaS Platform
                </div>
                <h1 class="mt-6 text-5xl font-bold leading-tight tracking-tight md:text-6xl">
                    Invoice management
                    <span class="block text-gradient">built for every business</span>
                </h1>
                <p class="mt-6 max-w-xl text-lg text-gray-500">
                    Create invoices, collect payments via Stripe, and manage customers and products — all in your own secure workspace. Start free for 14 days.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="group inline-flex items-center gradient-primary text-white px-6 py-3 rounded-lg font-medium shadow-glow transition hover:opacity-95">
                        Start Free Trial <i class="fas fa-arrow-right ml-2 text-sm transition group-hover:translate-x-1"></i>
                    </a>
                    <a href="#pricing" class="inline-flex items-center border border-blue-500/20 px-6 py-3 rounded-lg font-medium hover:bg-blue-500/5 transition">
                        View Pricing
                    </a>
                </div>
                <div class="mt-8 flex items-center gap-6 text-sm text-gray-500">
                    <div class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> No credit card</div>
                    <div class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> 14-day trial</div>
                    <div class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> Cancel anytime</div>
                </div>
            </div>
            <div class="relative animate-fade-up" style="animation-delay: 120ms">
                <div class="absolute -inset-4 rounded-3xl gradient-primary opacity-20 blur-2xl"></div>
                <img src="{{ asset('images/hero-invoice.jpg') }}" alt="Invoice and payment dashboard" width="1536" height="1024" class="relative rounded-2xl shadow-glow animate-float">
                {{-- Floating tags --}}
                <div class="absolute -left-4 top-8 hidden rounded-xl border border-gray-200 bg-white/90 px-3 py-2 text-xs shadow-card backdrop-blur md:flex md:items-center md:gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600">
                        <i class="fas fa-check text-xs"></i>
                    </div>
                    <div>
                        <div class="font-semibold">Payment received</div>
                        <div class="text-gray-500">$4,200 from Acme</div>
                    </div>
                </div>
                <div class="absolute -bottom-4 right-2 hidden rounded-xl border border-gray-200 bg-white/90 px-3 py-2 text-xs shadow-card backdrop-blur md:flex md:items-center md:gap-2">
                    <i class="fas fa-trending-up text-blue-500"></i>
                    <div>
                        <div class="font-semibold">+38% MoM</div>
                        <div class="text-gray-500">Revenue growth</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Logos strip --}}
        <div class="mt-20">
            <p class="text-center text-xs uppercase tracking-widest text-gray-400">Trusted by 10,000+ teams worldwide</p>
            <div class="mt-6 grid grid-cols-2 items-center gap-8 opacity-60 sm:grid-cols-3 md:grid-cols-6">
                @foreach(['Acme Corp', 'Northwind', 'Lumen', 'Globex', 'Initech', 'Vertex'] as $logo)
                    <div class="text-center text-sm font-semibold tracking-tight text-gray-400">{{ $logo }}</div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section id="features" class="border-t border-gray-200/60 py-24">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Features</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">Everything you need</h2>
            <p class="mt-4 text-gray-500">A complete toolkit to run invoicing without the chaos.</p>
        </div>
        <div class="mt-16 grid gap-6 md:grid-cols-3">
            @php
                $features = [
                    ['icon' => 'fa-file-invoice', 'title' => 'Invoice Management', 'desc' => 'Create, send, and track invoices with PDF export and instant email delivery.'],
                    ['icon' => 'fa-credit-card', 'title' => 'Payment Gateway', 'desc' => 'Enable Stripe on every invoice. Each organization manages their own keys.'],
                    ['icon' => 'fa-users', 'title' => 'Team Collaboration', 'desc' => 'Invite team members with role-based access. Your data stays isolated.'],
                    ['icon' => 'fa-chart-bar', 'title' => 'Reports & Analytics', 'desc' => 'Real-time dashboards on revenue, overdue invoices and customer health.'],
                    ['icon' => 'fa-shield-alt', 'title' => 'Secure Workspace', 'desc' => 'Bank-grade encryption with isolated multi-tenant architecture.'],
                    ['icon' => 'fa-bolt', 'title' => 'Automations', 'desc' => 'Recurring invoices, payment reminders and webhooks that just work.'],
                ];
            @endphp
            @foreach($features as $f)
            <div class="feature-card group relative rounded-2xl border border-gray-200 bg-white p-6 transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card">
                <div class="feature-icon flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 transition">
                    <i class="fas {{ $f['icon'] }}"></i>
                </div>
                <h3 class="mt-5 text-lg font-semibold">{{ $f['title'] }}</h3>
                <p class="mt-2 text-sm text-gray-500">{{ $f['desc'] }}</p>
                <div class="bottom-glow absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-blue-500/40 to-transparent opacity-0 transition"></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Advanced features --}}
<section id="advanced" class="border-t border-gray-200/60 bg-gray-50/50 py-24">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Built for billing pros</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">Powerful invoicing, made effortless</h2>
            <p class="mt-4 text-gray-500">Everything from drag-and-drop building to recurring billing and proposals — in one workspace.</p>
        </div>
        <div class="mt-16 grid gap-6 lg:grid-cols-2">
            {{-- Invoice builder --}}
            <article class="feature-card group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-8 transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card">
                <div class="flex items-center gap-3">
                    <div class="feature-icon flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 transition">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Drag-and-drop invoice builder</h3>
                </div>
                <p class="mt-3 text-sm text-gray-500">Build pixel-perfect invoices in seconds. Drag line items, apply taxes and discounts, drop in your logo, and add custom fields for POs or project codes.</p>
                <div class="mt-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span class="font-semibold text-gray-900">INV-1043</span>
                        <span>Acme Corp</span>
                    </div>
                    <div class="mt-3 space-y-2">
                        @foreach([['Design sprint', '10 h', '$1,500'], ['Implementation', '24 h', '$3,600'], ['Hosting (monthly)', '1', '$120']] as $row)
                        <div class="flex items-center justify-between rounded-lg border border-dashed border-gray-300 bg-gray-100/50 px-3 py-2 text-xs">
                            <span class="font-medium">{{ $row[0] }}</span>
                            <span class="text-gray-500">{{ $row[1] }}</span>
                            <span class="font-semibold">{{ $row[2] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs">
                        <span class="text-gray-500">Tax 10% · Discount 5%</span>
                        <span class="text-base font-bold">$5,283.00</span>
                    </div>
                </div>
                <ul class="mt-5 grid grid-cols-2 gap-2 text-xs text-gray-500">
                    @foreach(['Line items', 'Taxes & discounts', 'Logo upload', 'Custom fields'] as $c)
                    <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-[10px]"></i>{{ $c }}</li>
                    @endforeach
                </ul>
            </article>

            {{-- Recurring --}}
            <article class="feature-card group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-8 transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card">
                <div class="flex items-center gap-3">
                    <div class="feature-icon flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 transition">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Recurring invoices</h3>
                </div>
                <p class="mt-3 text-sm text-gray-500">Auto-send invoices on any schedule — weekly, monthly, yearly. Pause, resume or clone any recurring series in a single click.</p>
                <div class="mt-6 space-y-2">
                    @foreach([
                        ['c' => 'Northwind Retainer', 'f' => 'Every 1st · Monthly', 's' => 'Active', 'color' => 'emerald'],
                        ['c' => 'Lumen Hosting', 'f' => 'Every 15th · Monthly', 's' => 'Paused', 'color' => 'amber'],
                        ['c' => 'Globex SaaS', 'f' => 'Yearly · Jan 1', 's' => 'Active', 'color' => 'emerald'],
                    ] as $r)
                    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs">
                        <div>
                            <div class="font-semibold">{{ $r['c'] }}</div>
                            <div class="text-gray-500">{{ $r['f'] }}</div>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $r['color'] === 'emerald' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">{{ $r['s'] }}</span>
                    </div>
                    @endforeach
                </div>
                <ul class="mt-5 grid grid-cols-2 gap-2 text-xs text-gray-500">
                    @foreach(['Auto-send on schedule', 'Pause & resume', 'Clone series', 'Smart retries'] as $c)
                    <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-[10px]"></i>{{ $c }}</li>
                    @endforeach
                </ul>
            </article>

            {{-- Estimates --}}
            <article class="feature-card group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-8 transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card">
                <div class="flex items-center gap-3">
                    <div class="feature-icon flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 transition">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Estimates & quotes</h3>
                </div>
                <p class="mt-3 text-sm text-gray-500">Send a quote, let your client approve online, and watch it auto-convert into an invoice — no copy-paste, no double entry.</p>
                <div class="mt-6 flex items-center justify-between gap-3">
                    @foreach([['Quote sent', true], ['Client approves', true], ['Auto invoice', false]] as $i => $s)
                    <div class="flex flex-1 flex-col items-center text-center">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-xs font-semibold {{ $s[1] ? 'gradient-primary text-white' : 'border border-dashed border-blue-500/40 text-blue-500' }}">
                            @if($s[1])<i class="fas fa-check text-xs"></i>@else{{ $i + 1 }}@endif
                        </div>
                        <div class="mt-2 text-xs font-medium">{{ $s[0] }}</div>
                    </div>
                    @endforeach
                </div>
                <ul class="mt-5 grid grid-cols-2 gap-2 text-xs text-gray-500">
                    @foreach(['Online approval', 'Auto-convert to invoice', 'Version history', 'Expiry dates'] as $c)
                    <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-[10px]"></i>{{ $c }}</li>
                    @endforeach
                </ul>
            </article>

            {{-- Templates --}}
            <article class="feature-card group relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-8 transition hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card">
                <div class="flex items-center gap-3">
                    <div class="feature-icon flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 transition">
                        <i class="fas fa-palette"></i>
                    </div>
                    <h3 class="text-xl font-semibold">Invoice templates</h3>
                </div>
                <p class="mt-3 text-sm text-gray-500">Choose from 10+ professionally designed templates. Need more? Add custom CSS to match your brand exactly.</p>
                <div class="mt-6 grid grid-cols-5 gap-2">
                    @foreach(['Modern', 'Classic', 'Minimal', 'Bold', 'Elegant', 'Pro', 'Studio', 'Mono', 'Serif', 'Custom'] as $i => $t)
                    <div class="aspect-[3/4] rounded-md border text-[9px] font-medium leading-none flex items-end justify-center pb-1 {{ $i === 0 ? 'border-blue-500 gradient-primary text-white' : 'border-gray-200 bg-white text-gray-500' }}">{{ $t }}</div>
                    @endforeach
                </div>
                <ul class="mt-5 grid grid-cols-2 gap-2 text-xs text-gray-500">
                    @foreach(['10+ designs', 'Brand colors', 'Custom CSS', 'PDF-ready'] as $c)
                    <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-[10px]"></i>{{ $c }}</li>
                    @endforeach
                </ul>
            </article>
        </div>
    </div>
</section>

{{-- Dashboard preview --}}
<section id="dashboard" class="border-t border-gray-200/60 py-24">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Product</p>
                <h2 class="mt-3 text-4xl font-bold tracking-tight">A workspace your whole team will love</h2>
                <p class="mt-4 text-gray-500">Real-time invoice tracking, beautiful dashboards, and a focused UI that gets out of your way.</p>
                <ul class="mt-6 space-y-4">
                    @foreach([
                        ['icon' => 'fa-magic', 'title' => 'Intelligent suggestions', 'desc' => 'Auto-fill items, taxes, and reminders based on history.'],
                        ['icon' => 'fa-clock', 'title' => 'Faster than ever', 'desc' => 'Send a polished invoice in under 30 seconds.'],
                        ['icon' => 'fa-globe', 'title' => 'Multi-currency', 'desc' => 'Bill in 135+ currencies with live conversion.'],
                    ] as $b)
                    <li class="flex gap-4">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600">
                            <i class="fas {{ $b['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="font-semibold">{{ $b['title'] }}</div>
                            <div class="text-sm text-gray-500">{{ $b['desc'] }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            {{-- Mock dashboard --}}
            <div class="relative">
                <div class="absolute -inset-2 rounded-3xl gradient-primary opacity-20 blur-2xl"></div>
                <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-glow">
                    <div class="flex items-center gap-1.5 border-b border-gray-200 bg-gray-50/60 px-4 py-3">
                        <span class="h-2.5 w-2.5 rounded-full bg-red-400"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-yellow-400"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-green-400"></span>
                        <span class="ml-3 text-xs text-gray-400">app.reconx.com/dashboard</span>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-3 gap-3">
                            @foreach([['Revenue', '$48,250', '+12%'], ['Outstanding', '$8,420', '-4%'], ['Paid invoices', '182', '+9%']] as $s)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                                <div class="text-[10px] uppercase text-gray-400">{{ $s[0] }}</div>
                                <div class="mt-1 text-lg font-bold">{{ $s[1] }}</div>
                                <div class="text-[10px] font-medium text-blue-600">{{ $s[2] }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-5 flex h-32 items-end gap-2 rounded-xl bg-gray-50 p-3">
                            @foreach([45,70,35,80,55,90,60,75,50,95,65,85] as $h)
                            <div class="flex-1 rounded-t gradient-primary opacity-80" style="height:{{ $h }}%"></div>
                            @endforeach
                        </div>
                        <div class="mt-4 space-y-2">
                            @foreach([['INV-1042','Acme Corp','Paid','$4,200'], ['INV-1041','Northwind','Pending','$1,890'], ['INV-1040','Lumen Inc','Paid','$3,450']] as $r)
                            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs">
                                <div class="flex items-center gap-3">
                                    <span class="font-mono text-gray-400">{{ $r[0] }}</span>
                                    <span class="font-medium">{{ $r[1] }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $r[2] === 'Paid' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600' }}">{{ $r[2] }}</span>
                                    <span class="font-semibold">{{ $r[3] }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Stats --}}
<section class="border-t border-gray-200/60 py-20">
    <div class="mx-auto grid max-w-7xl gap-8 px-6 md:grid-cols-4">
        @foreach([['10k+', 'Active businesses'], ['$2.4B', 'Invoiced annually'], ['99.99%', 'Uptime SLA'], ['4.9/5', 'Customer rating']] as $s)
        <div class="text-center">
            <div class="text-gradient text-4xl font-bold">{{ $s[0] }}</div>
            <div class="mt-2 text-sm text-gray-500">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- Testimonials --}}
<section class="border-t border-gray-200/60 py-24">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Loved by teams</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">What our customers say</h2>
        </div>
        <div class="mt-16 grid gap-6 md:grid-cols-3">
            @foreach([
                ['name' => 'Sarah Chen', 'role' => 'CFO, Lumen Inc', 'quote' => 'ReconX cut our invoicing time by 70%. The Stripe integration just works.'],
                ['name' => 'Marcus Lee', 'role' => 'Founder, Northwind', 'quote' => 'Finally a tool that looks great and gets out of the way. My team adopted it in a day.'],
                ['name' => 'Priya Nair', 'role' => 'Ops Lead, Acme', 'quote' => 'The dashboards are stunning and the automations saved us hours every week.'],
            ] as $t)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-card">
                <div class="flex gap-0.5 text-blue-500">
                    @for($i = 0; $i < 5; $i++)
                    <i class="fas fa-star text-sm"></i>
                    @endfor
                </div>
                <p class="mt-4 text-sm leading-relaxed text-gray-900">"{{ $t['quote'] }}"</p>
                <div class="mt-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full gradient-primary text-sm font-semibold text-white">
                        {{ collect(explode(' ', $t['name']))->map(fn($n) => $n[0])->join('') }}
                    </div>
                    <div>
                        <div class="text-sm font-semibold">{{ $t['name'] }}</div>
                        <div class="text-xs text-gray-500">{{ $t['role'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Pricing --}}
<section id="pricing" class="border-t border-gray-200/60 py-24">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">Pricing</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">Simple pricing</h2>
            <p class="mt-4 text-gray-500">Choose a plan that fits your business. All plans include a 14-day free trial.</p>
            <div class="mt-8 inline-flex items-center rounded-full border border-gray-200 bg-white p-1 text-sm">
                <button id="billingMonthly" class="rounded-full px-4 py-1.5 transition gradient-primary text-white shadow-soft">Monthly</button>
                <button id="billingYearly" class="rounded-full px-4 py-1.5 transition text-gray-500">Yearly <span class="ml-1 rounded-full bg-emerald-500/15 px-1.5 py-0.5 text-[10px] font-semibold text-emerald-600">-20%</span></button>
            </div>
        </div>
        <div class="mt-16 grid gap-6 lg:grid-cols-3">
            @php
                $plans = [
                    ['name' => 'Starter', 'monthly' => 0, 'yearly' => 0, 'desc' => 'Perfect for freelancers and small businesses getting started.', 'features' => ['Up to 25 invoices/month', '1 user', 'PDF export', 'Email invoices'], 'popular' => false],
                    ['name' => 'Professional', 'monthly' => 30, 'yearly' => 24, 'desc' => 'For growing businesses that need payment collection.', 'features' => ['Unlimited invoices', '5 users', 'Stripe payments', 'Reports & analytics', 'Webhook integrations'], 'popular' => true],
                    ['name' => 'Business', 'monthly' => 80, 'yearly' => 64, 'desc' => 'Advanced features for teams with high volume invoicing.', 'features' => ['Unlimited everything', '25 users', 'Priority support', 'Custom branding', 'API access'], 'popular' => false],
                ];
            @endphp
            @foreach($plans as $p)
            <div class="relative rounded-2xl border bg-white p-8 transition {{ $p['popular'] ? 'border-blue-500 shadow-glow lg:-translate-y-4' : 'border-gray-200 hover:-translate-y-1 hover:border-blue-500/30 hover:shadow-card' }}">
                @if($p['popular'])
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full gradient-primary px-4 py-1 text-xs font-semibold text-white shadow-soft">MOST POPULAR</div>
                @endif
                <h3 class="text-xl font-semibold">{{ $p['name'] }}</h3>
                <div class="mt-4 flex items-baseline gap-1">
                    <span class="price-monthly text-5xl font-bold tracking-tight">${{ $p['monthly'] }}</span>
                    <span class="price-yearly text-5xl font-bold tracking-tight hidden">${{ $p['yearly'] }}</span>
                    <span class="text-sm text-gray-500">/month</span>
                </div>
                @if($p['monthly'] > 0)
                <div class="price-yearly hidden mt-1 text-xs text-gray-500">Billed ${{ $p['yearly'] * 12 }}/year</div>
                @endif
                <p class="mt-3 text-sm text-gray-500">{{ $p['desc'] }}</p>
                <ul class="mt-6 space-y-3">
                    @foreach($p['features'] as $f)
                    <li class="flex items-start gap-3 text-sm">
                        <i class="fas fa-check mt-0.5 text-blue-500 text-xs"></i>
                        <span>{{ $f }}</span>
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="mt-8 block w-full text-center px-4 py-2.5 rounded-lg font-medium transition {{ $p['popular'] ? 'gradient-primary text-white hover:opacity-95' : 'border border-gray-200 text-gray-700 hover:bg-gray-50' }}">Start Free Trial</a>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FAQ --}}
<section id="faq" class="border-t border-gray-200/60 py-24">
    <div class="mx-auto max-w-3xl px-6">
        <div class="text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-600">FAQ</p>
            <h2 class="mt-3 text-4xl font-bold tracking-tight">Questions, answered</h2>
        </div>
        <div class="mt-10 space-y-2">
            @foreach([
                ['q' => 'Do I need a credit card to start?', 'a' => 'No. You can start your 14-day free trial without entering any payment details.'],
                ['q' => 'Can I cancel anytime?', 'a' => 'Yes — cancel from your dashboard at any time. No long-term commitments.'],
                ['q' => 'Do you support international payments?', 'a' => 'Yes. We support 135+ currencies and process payments via Stripe globally.'],
                ['q' => 'Is my data secure?', 'a' => 'All data is encrypted at rest and in transit. We use isolated multi-tenant architecture for organization-level isolation.'],
                ['q' => 'Can I invite my team?', 'a' => 'Of course. Pro and Business plans support multiple users with role-based access control.'],
            ] as $faq)
            <div class="rounded-xl border border-gray-200">
                <button class="faq-toggle flex w-full items-center justify-between px-5 py-4 text-left text-sm font-medium hover:bg-gray-50 rounded-xl transition">
                    {{ $faq['q'] }}
                    <i class="fas fa-chevron-down faq-arrow text-xs text-gray-400 transition-transform duration-200"></i>
                </button>
                <div class="hidden px-5 pb-4 text-sm text-gray-500">{{ $faq['a'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="px-6 pb-24 pt-12">
    <div class="relative mx-auto max-w-5xl overflow-hidden rounded-3xl gradient-primary p-12 text-center text-white shadow-glow md:p-16">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <h2 class="relative text-4xl font-bold tracking-tight md:text-5xl">Ready to get paid faster?</h2>
        <p class="relative mx-auto mt-4 max-w-xl opacity-90">Join thousands of businesses streamlining their invoicing with ReconX.</p>
        <div class="relative mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('register') }}" class="inline-flex items-center bg-white text-blue-600 px-6 py-3 rounded-lg font-medium hover:bg-gray-50 transition">
                Start your free trial <i class="fas fa-arrow-right ml-2 text-sm"></i>
            </a>
            <a href="#pricing" class="inline-flex items-center border border-white/30 bg-transparent text-white px-6 py-3 rounded-lg font-medium hover:bg-white/10 transition">
                Talk to sales
            </a>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="border-t border-gray-200/60 py-12">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-8 md:grid-cols-4">
            <div>
                <div class="flex items-center gap-2">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg gradient-primary text-white">
                        <i class="fas fa-bolt text-sm"></i>
                    </div>
                    <span class="text-lg font-bold tracking-tight">ReconX</span>
                </div>
                <p class="mt-3 max-w-xs text-sm text-gray-500">Invoice management built for every business.</p>
            </div>
            @foreach([
                ['title' => 'Product', 'links' => ['Features', 'Pricing', 'Integrations', 'Changelog']],
                ['title' => 'Company', 'links' => ['About', 'Blog', 'Careers', 'Contact']],
                ['title' => 'Resources', 'links' => ['Docs', 'Help center', 'Status', 'API']],
            ] as $col)
            <div>
                <div class="text-sm font-semibold">{{ $col['title'] }}</div>
                <ul class="mt-3 space-y-2">
                    @foreach($col['links'] as $l)
                    <li><a href="#" class="text-sm text-gray-500 transition hover:text-gray-900">{{ $l }}</a></li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>
        <div class="mt-10 border-t border-gray-200/60 pt-6 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} ReconX. All rights reserved.
        </div>
    </div>
</footer>

{{-- Scroll to top --}}
<button id="scrollTopBtn" class="hidden fixed bottom-6 right-6 z-50 flex h-11 w-11 items-center justify-center rounded-full gradient-primary text-white shadow-glow transition hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>
@endsection
