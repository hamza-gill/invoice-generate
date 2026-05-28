<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate #{{ $estimate->estimate_number }} - {{ $globalSettings->company_name ?? config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Top Bar --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if(!empty($globalSettings->logo_path))
                    <img src="{{ asset('storage/' . $globalSettings->logo_path) }}" alt="Logo" class="h-8">
                @endif
                <span class="text-lg font-bold text-gray-800">{{ $globalSettings->company_name ?? config('app.name') }}</span>
            </div>
            <div>
                <span class="px-4 py-1.5 text-sm font-semibold rounded-full
                    @if($estimate->status === 'approved') text-green-600 bg-green-50
                    @elseif($estimate->status === 'declined') text-red-600 bg-red-50
                    @elseif($estimate->status === 'expired') text-orange-600 bg-orange-50
                    @else text-blue-600 bg-blue-50 @endif">
                    {{ ucfirst($estimate->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center">
                <i class="fas fa-check-circle mr-2 text-green-600"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center">
                <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

            {{-- Estimate Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-8 sm:px-12 py-8 text-white">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold mb-1">Estimate</h1>
                        <p class="text-blue-200 text-lg">#{{ $estimate->estimate_number }}</p>
                    </div>
                    <div class="mt-4 sm:mt-0 text-right">
                        <p class="text-blue-200 text-sm">Amount</p>
                        @php
                            $currency = $globalSettings->base_currency ?? '$';
                            $subtotal = $estimate->items->sum(fn($item) => $item->quantity * $item->unit_price);
                            $discount = $estimate->discount ?? 0;
                            $total = max(0, $subtotal - $discount);
                        @endphp
                        <p class="text-3xl font-bold">{{ $currency }}{{ number_format($total, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="px-8 sm:px-12 py-8">

                {{-- Company & Client Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                    <div>
                        <p class="text-sm font-semibold text-gray-400 uppercase mb-3">From</p>
                        <h3 class="font-bold text-gray-800 text-lg">{{ $globalSettings->company_name ?? config('app.name') }}</h3>
                        <p class="text-gray-600">{{ $globalSettings->address ?? '' }}</p>
                        @if($globalSettings->contact_email)
                            <p class="text-gray-600">{{ $globalSettings->contact_email }}</p>
                        @endif
                        @if(!empty($globalSettings->phone))
                            <p class="text-gray-600">{{ $globalSettings->phone }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-400 uppercase mb-3">Prepared For</p>
                        <h3 class="font-bold text-gray-800 text-lg">{{ $estimate->customer->full_name ?? 'N/A' }}</h3>
                        <p class="text-gray-600">{{ $estimate->customer->email ?? '' }}</p>
                        <p class="text-gray-600">{{ $estimate->customer->address ?? '' }}</p>
                        @if($estimate->customer->city)
                            <p class="text-gray-600">{{ $estimate->customer->city }}, {{ $estimate->customer->state }} {{ $estimate->customer->postal_code }}</p>
                        @endif
                    </div>
                </div>

                {{-- Dates --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs text-gray-500 uppercase font-medium">Issue Date</p>
                        <p class="font-semibold text-gray-800 mt-1">{{ \Carbon\Carbon::parse($estimate->issue_date)->format('M d, Y') }}</p>
                    </div>
                    @if($estimate->valid_until)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-500 uppercase font-medium">Valid Until</p>
                            <p class="font-semibold text-gray-800 mt-1">{{ \Carbon\Carbon::parse($estimate->valid_until)->format('M d, Y') }}</p>
                        </div>
                    @endif
                    @if($estimate->project_address)
                        <div class="bg-gray-50 rounded-xl p-4 col-span-2">
                            <p class="text-xs text-gray-500 uppercase font-medium">Project Address</p>
                            <p class="font-semibold text-gray-800 mt-1">{{ $estimate->project_address }}</p>
                        </div>
                    @endif
                </div>

                {{-- Line Items --}}
                <div class="overflow-x-auto mb-8">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-gray-200">
                                <th class="text-left pb-3 text-sm font-semibold text-gray-600">Description</th>
                                <th class="text-center pb-3 text-sm font-semibold text-gray-600">Qty</th>
                                <th class="text-right pb-3 text-sm font-semibold text-gray-600">Unit Price</th>
                                <th class="text-right pb-3 text-sm font-semibold text-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($estimate->items as $item)
                                <tr>
                                    <td class="py-4 text-gray-800">{{ $item->description ?? ($item->product->name ?? 'N/A') }}</td>
                                    <td class="py-4 text-center text-gray-600">{{ $item->quantity }}</td>
                                    <td class="py-4 text-right text-gray-600">{{ $currency }}{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-4 text-right font-semibold text-gray-800">{{ $currency }}{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="flex justify-end mb-10">
                    <div class="w-72">
                        <div class="flex justify-between py-2 text-gray-600">
                            <span>Subtotal</span>
                            <span>{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between py-2 text-red-600">
                                <span>Discount</span>
                                <span>-{{ $currency }}{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-3 border-t-2 border-gray-800 text-xl font-bold">
                            <span>Total</span>
                            <span>{{ $currency }}{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Custom Fields --}}
                @if($estimate->custom_fields && count($estimate->custom_fields) > 0)
                    <div class="border-t border-gray-200 pt-6 mb-8">
                        <h3 class="font-semibold text-gray-800 mb-3">Additional Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($estimate->custom_fields as $field)
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500 uppercase">{{ $field['key'] }}</p>
                                    <p class="text-gray-800 font-medium mt-1">{{ $field['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Notes --}}
                @if(!empty($estimate->notes))
                    <div class="border-t border-gray-200 pt-6 mb-8">
                        <h3 class="font-semibold text-gray-800 mb-2">Notes</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $estimate->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            @if(in_array($estimate->status, ['sent', 'viewed']))
                <div class="bg-gray-50 border-t border-gray-200 px-8 sm:px-12 py-6">
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <form action="{{ route('estimates.public.approve', $estimate->id) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition shadow-sm text-lg">
                                <i class="fas fa-check-circle mr-2"></i>Approve Estimate
                            </button>
                        </form>
                        <form action="{{ route('estimates.public.decline', $estimate->id) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-white text-red-600 border-2 border-red-300 rounded-xl font-semibold hover:bg-red-50 transition text-lg">
                                <i class="fas fa-times-circle mr-2"></i>Decline Estimate
                            </button>
                        </form>
                    </div>
                    <p class="text-center text-xs text-gray-400 mt-4">
                        By clicking "Approve", you agree to the terms outlined in this estimate.
                    </p>
                </div>
            @elseif($estimate->status === 'approved')
                <div class="bg-green-50 border-t border-green-200 px-8 sm:px-12 py-6 text-center">
                    <i class="fas fa-check-circle text-green-600 text-3xl mb-2"></i>
                    <p class="font-semibold text-green-800 text-lg">This estimate has been approved</p>
                    <p class="text-green-600 text-sm mt-1">Thank you for your approval.</p>
                </div>
            @elseif($estimate->status === 'declined')
                <div class="bg-red-50 border-t border-red-200 px-8 sm:px-12 py-6 text-center">
                    <i class="fas fa-times-circle text-red-600 text-3xl mb-2"></i>
                    <p class="font-semibold text-red-800 text-lg">This estimate has been declined</p>
                    <p class="text-red-600 text-sm mt-1">If you'd like to discuss further, please contact us.</p>
                </div>
            @elseif($estimate->status === 'expired')
                <div class="bg-orange-50 border-t border-orange-200 px-8 sm:px-12 py-6 text-center">
                    <i class="fas fa-clock text-orange-600 text-3xl mb-2"></i>
                    <p class="font-semibold text-orange-800 text-lg">This estimate has expired</p>
                    <p class="text-orange-600 text-sm mt-1">Please contact us if you'd like a new estimate.</p>
                </div>
            @elseif($estimate->status === 'converted')
                <div class="bg-indigo-50 border-t border-indigo-200 px-8 sm:px-12 py-6 text-center">
                    <i class="fas fa-file-invoice-dollar text-indigo-600 text-3xl mb-2"></i>
                    <p class="font-semibold text-indigo-800 text-lg">This estimate has been converted to an invoice</p>
                    <p class="text-indigo-600 text-sm mt-1">An invoice has been generated based on this estimate.</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="text-center py-8 text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} {{ $globalSettings->company_name ?? config('app.name') }}. All rights reserved.</p>
        </div>
    </div>

</body>
</html>
