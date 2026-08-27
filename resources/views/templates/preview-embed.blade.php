<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $template->name }} — Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        html, body { margin: 0; padding: 0; background: #f3f4f6; }
    </style>
</head>
<body>

@php
    $config = $template->config ?? [];
    $primary = $config['primary_color'] ?? '#3B82F6';
    $secondary = $config['secondary_color'] ?? '#93C5FD';
    $font = $config['font_family'] ?? 'Arial';
    $headerStyle = $config['header_style'] ?? 'default';

    $isGradientHeader = str_contains($headerStyle, 'gradient') || $template->slug === 'creative-agency';
    $headerBg = $isGradientHeader ? "linear-gradient(135deg, {$primary}, {$secondary})" : $primary;
    $headerTextColor = $template->slug === 'construction' ? '#1f2937' : '#ffffff';

    $templateMeta = [
        'classic-professional' => ['icon' => 'fa-landmark', 'label' => 'CLASSIC'],
        'modern-minimal'      => ['icon' => 'fa-feather-alt', 'label' => 'MINIMAL'],
        'bold-corporate'      => ['icon' => 'fa-building', 'label' => 'CORPORATE'],
        'creative-agency'     => ['icon' => 'fa-paint-brush', 'label' => 'CREATIVE'],
        'tech-startup'        => ['icon' => 'fa-microchip', 'label' => 'TECH'],
        'elegant'             => ['icon' => 'fa-gem', 'label' => 'ELEGANT'],
        'construction'        => ['icon' => 'fa-hard-hat', 'label' => 'BUILD'],
        'medical'             => ['icon' => 'fa-heartbeat', 'label' => 'MEDICAL'],
        'legal'               => ['icon' => 'fa-gavel', 'label' => 'LEGAL'],
        'freelancer'          => ['icon' => 'fa-rocket', 'label' => 'FREELANCE'],
    ];
    $meta = $templateMeta[$template->slug] ?? ['icon' => 'fa-file-invoice', 'label' => 'TEMPLATE'];
@endphp

<div class="p-6">
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
                    <p class="text-xs text-gray-400">Generated with <span class="font-semibold" style="color: {{ $primary }};">{{ $template->name }}</span> template &middot; Powered by Inveqi</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
