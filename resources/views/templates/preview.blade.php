@extends('layouts.auth.app')

@section('title', 'Preview: ' . $template->name . ' - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    @php
        $config = $template->config ?? [];
        $primary = $config['primary_color'] ?? '#3B82F6';
        $secondary = $config['secondary_color'] ?? '#93C5FD';
        $font = $config['font_family'] ?? 'Arial';
        $headerStyle = $config['header_style'] ?? 'default';
        $isActive = ($activeTemplateId ?? null) === $template->id;

        $isGradientHeader = str_contains($headerStyle, 'gradient') || $template->slug === 'creative-agency';
        $headerBg = $isGradientHeader ? "linear-gradient(135deg, {$primary}, {$secondary})" : $primary;
        $headerTextColor = $template->slug === 'construction' ? '#1f2937' : '#ffffff';

        $templateMeta = [
            'classic-professional' => ['icon' => 'fa-landmark', 'label' => 'CLASSIC', 'tagline' => 'Timeless & Trusted'],
            'modern-minimal'      => ['icon' => 'fa-feather-alt', 'label' => 'MINIMAL', 'tagline' => 'Clean & Modern'],
            'bold-corporate'      => ['icon' => 'fa-building', 'label' => 'CORPORATE', 'tagline' => 'Bold & Powerful'],
            'creative-agency'     => ['icon' => 'fa-paint-brush', 'label' => 'CREATIVE', 'tagline' => 'Vibrant & Expressive'],
            'tech-startup'        => ['icon' => 'fa-microchip', 'label' => 'TECH', 'tagline' => 'Code-Ready'],
            'elegant'             => ['icon' => 'fa-gem', 'label' => 'ELEGANT', 'tagline' => 'Refined & Graceful'],
            'construction'        => ['icon' => 'fa-hard-hat', 'label' => 'BUILD', 'tagline' => 'Industrial Strength'],
            'medical'             => ['icon' => 'fa-heartbeat', 'label' => 'MEDICAL', 'tagline' => 'Clean & Clinical'],
            'legal'               => ['icon' => 'fa-gavel', 'label' => 'LEGAL', 'tagline' => 'Formal & Precise'],
            'freelancer'          => ['icon' => 'fa-rocket', 'label' => 'FREELANCE', 'tagline' => 'Friendly & Flexible'],
        ];
        $meta = $templateMeta[$template->slug] ?? ['icon' => 'fa-file-invoice', 'label' => 'TEMPLATE', 'tagline' => 'Professional'];
    @endphp

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between shadow-sm sticky top-0 z-20">
        <div class="flex items-center space-x-4">
            <a href="{{ route('templates.index') }}" class="text-gray-600 hover:text-gray-800 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white" style="background: {{ $isGradientHeader ? $headerBg : $primary }};">
                    <i class="fas {{ $meta['icon'] }} text-sm"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $template->name }}</h2>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full" style="background: {{ $primary }};"></div>
                        @if($secondary !== $primary)
                            <div class="w-2 h-2 rounded-full" style="background: {{ $secondary }};"></div>
                        @endif
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-gray-400">{{ $meta['tagline'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('templates.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition text-sm">
                <i class="fas fa-th-large mr-2"></i>Back to Gallery
            </a>
            @if($isActive)
                <span class="px-4 py-2 bg-green-50 text-green-700 rounded-lg font-semibold text-sm">
                    <i class="fas fa-check-circle mr-1"></i> Currently Active
                </span>
            @else
                <form action="{{ route('templates.select', $template->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-white rounded-lg font-semibold transition text-sm hover:opacity-90" style="background: {{ $primary }};">
                        <i class="fas fa-check mr-2"></i>Select This Template
                    </button>
                </form>
            @endif
        </div>
    </header>

    {{-- Template Details Card --}}
    <div class="max-w-6xl mx-auto mt-8 mb-6 px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-800">{{ $template->name }}</h3>
                    <p class="text-gray-500 mt-1">{{ $template->description ?? 'No description available.' }}</p>
                </div>
                <div class="flex items-center space-x-5 text-sm text-gray-500 ml-6">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-400">Colors:</span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-6 h-6 rounded-full border border-gray-200 shadow-sm" style="background: {{ $primary }};"></div>
                            <div class="w-6 h-6 rounded-full border border-gray-200 shadow-sm" style="background: {{ $secondary }};"></div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-font text-gray-400"></i>
                        <span style="font-family: {{ $font }}, sans-serif;">{{ $font }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-layer-group text-gray-400"></i>
                        <span class="capitalize">{{ str_replace('-', ' ', $headerStyle) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Full-Size Invoice Preview --}}
    <div class="max-w-6xl mx-auto px-4 pb-12">
        <div class="rounded-2xl p-8 border border-gray-200" style="background: {{ $primary }}08;">
            <div class="bg-white rounded-xl shadow-lg mx-auto max-w-4xl overflow-hidden" style="font-family: {{ $font }}, sans-serif;">

                {{-- Invoice Header --}}
                <div class="px-10 py-8 flex items-start justify-between" style="background: {{ $headerBg }}; color: {{ $headerTextColor }};">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                                <i class="fas {{ $meta['icon'] }}" style="color: {{ $headerTextColor }};"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold tracking-widest uppercase" style="opacity: 0.7;">{{ $meta['label'] }}</div>
                                <h1 class="text-2xl font-bold">INVOICE</h1>
                            </div>
                        </div>
                        <p class="text-sm mt-1" style="opacity: 0.8;">#INV-2026-0125</p>
                        <p class="text-sm mt-1" style="opacity: 0.7;">123 Project Avenue, Suite 200</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">{{ $globalSettings->company_name ?? 'Your Company' }}</p>
                        <p class="text-sm" style="opacity: 0.8;">{{ $globalSettings->address ?? '456 Business Street' }}</p>
                        <p class="text-sm" style="opacity: 0.8;">{{ $globalSettings->contact_email ?? 'hello@company.com' }}</p>
                        @if($globalSettings->tax_id ?? false)
                            <p class="text-sm mt-1" style="opacity: 0.7;">Tax ID: {{ $globalSettings->tax_id }}</p>
                        @endif
                    </div>
                </div>

                {{-- Invoice Body --}}
                <div class="px-10 py-8">
                    {{-- Bill To & Dates --}}
                    <div class="grid grid-cols-2 gap-8 mb-8">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: {{ $primary }};">Bill To</p>
                            <p class="font-semibold text-gray-800">John Doe</p>
                            <p class="text-sm text-gray-500">john@example.com</p>
                            <p class="text-sm text-gray-500">456 Client Avenue, Suite 100</p>
                            <p class="text-sm text-gray-500">New York, NY 10001</p>
                        </div>
                        <div class="text-right">
                            <table class="ml-auto text-sm">
                                <tr>
                                    <td class="text-gray-400 pr-4 py-1 text-right">Issue Date:</td>
                                    <td class="font-semibold text-gray-700 py-1">May 20, 2026</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 pr-4 py-1 text-right">Due Date:</td>
                                    <td class="font-semibold text-gray-700 py-1">Jun 20, 2026</td>
                                </tr>
                                <tr>
                                    <td class="text-gray-400 pr-4 py-1 text-right">Status:</td>
                                    <td class="py-1">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background: {{ $primary }}15; color: {{ $primary }};">SENT</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- Line Items Table --}}
                    <table class="w-full mb-8">
                        <thead>
                            <tr>
                                <th class="pb-3 pt-3 px-4 text-left text-xs font-bold uppercase tracking-wider rounded-tl-lg" style="background: {{ $primary }}10; color: {{ $primary }}; border-bottom: 2px solid {{ $primary }};">Service</th>
                                <th class="pb-3 pt-3 px-4 text-left text-xs font-bold uppercase tracking-wider" style="background: {{ $primary }}10; color: {{ $primary }}; border-bottom: 2px solid {{ $primary }};">Description</th>
                                <th class="pb-3 pt-3 px-4 text-center text-xs font-bold uppercase tracking-wider" style="background: {{ $primary }}10; color: {{ $primary }}; border-bottom: 2px solid {{ $primary }};">Qty</th>
                                <th class="pb-3 pt-3 px-4 text-right text-xs font-bold uppercase tracking-wider" style="background: {{ $primary }}10; color: {{ $primary }}; border-bottom: 2px solid {{ $primary }};">Price</th>
                                <th class="pb-3 pt-3 px-4 text-right text-xs font-bold uppercase tracking-wider rounded-tr-lg" style="background: {{ $primary }}10; color: {{ $primary }}; border-bottom: 2px solid {{ $primary }};">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach([
                                ['Website Development', 'Full-stack web application build', '1', '$3,500.00', '$3,500.00'],
                                ['Logo & Brand Design', 'Corporate identity package', '1', '$800.00', '$800.00'],
                                ['SEO Optimization', 'On-page & technical SEO audit', '1', '$1,200.00', '$1,200.00'],
                                ['Monthly Hosting', 'Cloud hosting (12 months)', '12', '$25.00', '$300.00'],
                            ] as $i => $row)
                            <tr class="border-b border-gray-100 {{ $i % 2 === 1 ? 'bg-gray-50/50' : '' }}">
                                <td class="py-3 px-4 font-medium text-gray-800">{{ $row[0] }}</td>
                                <td class="py-3 px-4 text-sm text-gray-500">{{ $row[1] }}</td>
                                <td class="py-3 px-4 text-center text-sm text-gray-600">{{ $row[2] }}</td>
                                <td class="py-3 px-4 text-right text-sm text-gray-600">{{ $row[3] }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-gray-800">{{ $row[4] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Totals --}}
                    <div class="flex justify-end">
                        <div class="w-80">
                            <div class="flex justify-between py-2 text-sm text-gray-600 border-b border-gray-100">
                                <span>Subtotal</span>
                                <span class="font-medium">$5,800.00</span>
                            </div>
                            <div class="flex justify-between py-2 text-sm text-gray-500 border-b border-gray-100">
                                <span>Tax (10%)</span>
                                <span>$580.00</span>
                            </div>
                            <div class="flex justify-between py-2 text-sm text-red-500 border-b border-gray-100">
                                <span>Discount</span>
                                <span>-$250.00</span>
                            </div>
                            <div class="flex justify-between py-3 text-lg font-bold mt-1" style="border-top: 2px solid {{ $primary }};">
                                <span>Total</span>
                                <span style="color: {{ $primary }};">$6,130.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- Notes & Terms --}}
                    <div class="mt-10 pt-6 border-t border-gray-200 grid grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-sm font-bold uppercase tracking-wider mb-2" style="color: {{ $primary }};">Notes</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">Thank you for your business. Payment is due within 30 days of the invoice date. Late payments may be subject to a 1.5% monthly fee.</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold uppercase tracking-wider mb-2" style="color: {{ $primary }};">Terms & Conditions</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">All work remains the property of the provider until full payment. Revisions beyond the agreed scope will be billed separately.</p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-8 pt-4 border-t border-gray-100 text-center">
                        <p class="text-xs text-gray-400">Generated with <span class="font-semibold" style="color: {{ $primary }};">{{ $template->name }}</span> template &middot; Powered by ReconX</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({ icon: 'success', title: 'Success', text: '{{ session("success") }}', timer: 2500, showConfirmButton: false });
            });
        </script>
    @endif
@endsection
