@extends('layouts.auth.app')

@section('title', 'Invoice Templates - ' . ($globalSettings->company_name ?? config('app.name')))

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Invoice Templates</h1>
            <p class="text-gray-500 mt-1">Choose a template to customize the look of your invoices</p>
        </div>
    </div>

    @php
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
    @endphp

    {{-- Template Gallery Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($templates as $template)
            @php
                $config = $template->config ?? [];
                $primary = $config['primary_color'] ?? '#3B82F6';
                $secondary = $config['secondary_color'] ?? '#93C5FD';
                $font = $config['font_family'] ?? 'Arial';
                $headerStyle = $config['header_style'] ?? 'default';
                $meta = $templateMeta[$template->slug] ?? ['icon' => 'fa-file-invoice', 'label' => 'TEMPLATE', 'tagline' => 'Professional'];
                $isActive = $activeTemplateId === $template->id;

                $isGradientHeader = str_contains($headerStyle, 'gradient') || $template->slug === 'creative-agency';
                $isDarkHeader = in_array($headerStyle, ['dark-bar', 'dark-tech', 'formal', 'navy-gold', 'elegant']);
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border {{ $isActive ? 'border-blue-500 ring-2 ring-blue-100' : 'border-gray-100' }} overflow-hidden group hover:shadow-lg transition-all duration-300">
                {{-- Mini Invoice Preview --}}
                <a href="{{ route('templates.preview', $template->id) }}" class="block relative overflow-hidden">
                    <div class="h-56 p-4 flex flex-col" style="background: linear-gradient(180deg, {{ $primary }}08 0%, white 100%);">
                        {{-- Mini invoice card --}}
                        <div class="flex-1 bg-white rounded-lg shadow-sm border border-gray-200/80 overflow-hidden transform group-hover:scale-[1.02] transition-transform duration-300 flex flex-col">
                            {{-- Invoice header bar --}}
                            <div class="px-3 py-2.5 flex items-center justify-between"
                                 style="background: {{ $isGradientHeader ? 'linear-gradient(135deg, '.$primary.', '.$secondary.')' : $primary }};">
                                <div>
                                    <div class="text-[9px] font-bold tracking-wider" style="color: {{ $isDarkHeader || !$isGradientHeader ? 'white' : 'white' }}; opacity: 0.8;">{{ $meta['label'] }}</div>
                                    <div class="text-[10px] font-bold" style="color: {{ $template->slug === 'construction' ? '#1f2937' : 'white' }};">INVOICE #1043</div>
                                </div>
                                <div class="w-6 h-6 rounded-md flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                                    <i class="fas {{ $meta['icon'] }} text-[8px]" style="color: {{ $template->slug === 'construction' ? '#1f2937' : 'white' }};"></i>
                                </div>
                            </div>

                            {{-- Invoice body mockup --}}
                            <div class="flex-1 px-3 py-2 flex flex-col justify-between">
                                <div>
                                    {{-- Bill to --}}
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <div class="text-[7px] uppercase tracking-wider font-bold" style="color: {{ $primary }};">Bill To</div>
                                            <div class="text-[8px] text-gray-700 font-medium" style="font-family: {{ $font }}, sans-serif;">Acme Corporation</div>
                                            <div class="text-[7px] text-gray-400">acme@example.com</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-[7px] text-gray-400">Date: Jan 15, 2026</div>
                                            <div class="text-[7px] text-gray-400">Due: Feb 15, 2026</div>
                                        </div>
                                    </div>

                                    {{-- Line items table --}}
                                    <div class="rounded overflow-hidden border" style="border-color: {{ $primary }}15;">
                                        <div class="flex text-[6px] uppercase tracking-wider font-bold px-2 py-1" style="background: {{ $primary }}10; color: {{ $primary }};">
                                            <span class="flex-1">Item</span>
                                            <span class="w-8 text-center">Qty</span>
                                            <span class="w-12 text-right">Amount</span>
                                        </div>
                                        @foreach([['Design Sprint', '10', '$1,500'], ['Development', '24', '$3,600'], ['Hosting', '1', '$120']] as $row)
                                        <div class="flex text-[7px] px-2 py-[3px] border-t" style="border-color: {{ $primary }}08; font-family: {{ $font }}, sans-serif;">
                                            <span class="flex-1 text-gray-600">{{ $row[0] }}</span>
                                            <span class="w-8 text-center text-gray-400">{{ $row[1] }}</span>
                                            <span class="w-12 text-right font-medium text-gray-700">{{ $row[2] }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Total --}}
                                <div class="flex justify-end mt-2">
                                    <div class="text-right">
                                        <div class="text-[7px] text-gray-400">Subtotal: $5,220.00</div>
                                        <div class="text-[9px] font-bold px-2 py-0.5 rounded mt-0.5" style="color: {{ $primary }}; background: {{ $primary }}10;">Total: $5,283.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Active badge overlay --}}
                        @if($isActive)
                            <div class="absolute top-2 right-2 flex items-center gap-1 px-2 py-1 rounded-full bg-green-500 text-white text-[10px] font-bold shadow-sm">
                                <i class="fas fa-check-circle text-[8px]"></i> Active
                            </div>
                        @endif
                    </div>
                </a>

                {{-- Template Info --}}
                <div class="px-5 pb-5 pt-3">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-2 h-2 rounded-full" style="background: {{ $primary }};"></div>
                        @if($secondary && $secondary !== $primary)
                            <div class="w-2 h-2 rounded-full" style="background: {{ $secondary }};"></div>
                        @endif
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-gray-400">{{ $meta['tagline'] }}</span>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">{{ $template->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $template->description ?? 'No description' }}</p>

                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('templates.preview', $template->id) }}" class="text-xs font-medium hover:underline" style="color: {{ $primary }};">
                            <i class="fas fa-eye mr-1"></i> Preview
                        </a>

                        @if($isActive)
                            <span class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-semibold cursor-default">
                                <i class="fas fa-check mr-1"></i> Active
                            </span>
                        @else
                            <form action="{{ route('templates.select', $template->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-white rounded-lg text-xs font-semibold hover:opacity-90 transition" style="background: {{ $primary }};">
                                    <i class="fas fa-palette mr-1"></i> Select
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <i class="fas fa-palette text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Templates Available</h3>
                    <p class="text-gray-400">Templates will appear here once they are added to the system.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Custom CSS Section --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-code mr-2 text-blue-600"></i>Custom CSS
                </h3>
                <p class="text-sm text-gray-500 mt-1">Add custom CSS to override the default template styling on your invoices</p>
            </div>
        </div>

        <form action="{{ route('templates.customCss') }}" method="POST">
            @csrf
            <div class="mb-4">
                <textarea
                    name="custom_css"
                    id="customCss"
                    rows="10"
                    placeholder="/* Enter your custom CSS here */&#10;.invoice-header { background: #f3f4f6; }&#10;.invoice-total { font-size: 1.5rem; }"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 font-mono text-sm bg-gray-50"
                >{{ $customCss ?? '' }}</textarea>
            </div>

            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    CSS will be applied to the invoice PDF and public view.
                </p>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Save Custom CSS
                </button>
            </div>
        </form>
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
