@extends('layouts.auth.app')

@section('title', 'View Invoice - ReconX')
<?php $hideNavbar = true; ?>

@section('content')
    <header class="bg-white border-b border-gray-200 px-8 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('invoices.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-xl font-bold text-gray-800">Invoice Details</h2>
            </div>
            <div class="flex space-x-3">

                @if(!in_array($invoice->status, ['paid', 'void']))
                    <button id="editInvoiceBtn" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </button>
                    <button id="voidBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <i class="fas fa-ban mr-2"></i>Void
                    </button>
                @endif

                <!-- ⭐ ADDED: Copy Public Link Button -->
                <button id="copyLinkBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-link mr-2"></i>Copy Public Link
                </button>

                <button id="sendEmailBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-envelope mr-2"></i>Send Email
                </button>

                <button id="downloadPdfBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </button>
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-8">
        <div class="max-w-4xl mx-auto bg-white p-12 rounded-xl shadow-sm border border-gray-100" id="invoiceContent">

            <!-- ⭐ YOUR FULL ORIGINAL INVOICE DISPLAY CODE (unchanged) ⭐ -->

            <!-- Header Section -->
            <div class="flex justify-between mb-12">
                <div>
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">INVOICE</h1>
                    <p class="text-lg text-gray-600">#{{ $invoice->invoice_number }}</p>
                    @if(!empty($invoice->project_address))
                        <p class="text-sm font-bold text-gray-500 mb-1">PROJECT ADDRESS:</p>
                        <p class="text-gray-800 whitespace-pre-line">{{ $invoice->project_address }}</p>
                    @endif
                </div>
                <div class="text-right">
                    @if(!empty($globalSettings->logo_path))
                        <img src="{{ asset('storage/' . $globalSettings->logo_path) }}" alt="Logo" class="h-12 mb-2 ml-auto">
                    @endif
                    <h2 class="text-xl font-bold text-gray-800">{{ $globalSettings->company_name ?? config('app.name') }}</h2>
                    <p class="text-gray-600">{{ $globalSettings->address ?? '' }}</p>

                    @if($globalSettings->enable_tax_id && !empty($globalSettings->tax_id))
                        <p class="text-gray-600">Tax ID: {{ $globalSettings->tax_id }}</p>
                    @endif

                    @if($globalSettings->contact_email)
                        <p class="text-gray-600">
                            Email:
                            <a href="mailto:{{ $globalSettings->contact_email }}" class="text-blue-600 hover:underline">
                                {{ $globalSettings->contact_email }}
                            </a>
                        </p>
                    @endif
                </div>
            </div>

            <!-- Billing Info -->
            <div class="grid grid-cols-2 gap-8 mb-12">
                <div>
                    <p class="text-sm text-gray-500 mb-2">BILL TO:</p>
                    <p>{{ $invoice->customer->name ?? 'N/A' }}</p>
                    <p>{{ $invoice->customer->email ?? 'N/A' }}</p>
                    <p>{{ $invoice->customer->address ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <span class="text-gray-500">Issue Date:</span>
                        <span class="font-semibold">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('M d, Y') }}</span>
                        @if(!empty($globalSettings->enable_due_date) && $globalSettings->enable_due_date)
                            <span class="text-gray-500">Due Date:</span>
                            <span class="font-semibold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</span>
                        @endif

                        <span class="text-gray-500">Status:</span>
                        <span>
                        @php
                            $color = match($invoice->status) {
                                'sent' => 'bg-blue-100 text-blue-600',
                                'paid' => 'bg-green-100 text-green-600',
                                'void' => 'bg-red-100 text-red-600',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $color }}">
                            {{ strtoupper($invoice->status ?? 'N/A') }}
                        </span>
                    </span>
                    </div>
                </div>
            </div>

            <!-- Line Items -->
            @php
                $currency = $globalSettings->base_currency ?? '$';
                $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);
                $rushFee = $invoice->rush_enabled_value ? ($invoice->rush_fee ?? 0) : 0;
                if (!empty($globalSettings->enable_tax) && $globalSettings->enable_tax) {
                    $taxRate = $globalSettings->tax_percentage ? $globalSettings->tax_percentage / 100 : 0;
                    $taxAmount = ($subtotal + $rushFee) * $taxRate;
                } else {
                    $taxRate = 0;
                    $taxAmount = 0;
                }
                $total = $subtotal + $rushFee + $taxAmount;
            @endphp

            <table class="w-full mb-8">
                <thead class="border-b-2 border-gray-300">
                <tr class="text-left">
                    <th class="pb-4 text-sm font-semibold text-gray-600">SERVICE</th>
                    <th class="pb-4 text-sm font-semibold text-gray-600">DESCRIPTION</th>
                    <th class="pb-4 text-sm font-semibold text-gray-600 text-right">AMOUNT</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->product->description }}</td>
                        <td class="text-right">{{ $currency }}{{ number_format($item->quantity * $item->amount, 2) }}</td>
                    </tr>
                @endforeach

                @if($invoice->rush_enabled_value)
                    <tr class="bg-yellow-50 font-semibold">
                        <td>Rush Add-On ({{ ucfirst($invoice->rush_delivery_type) ?? 'Fast' }})</td>
                        <td>Expedite delivery service</td>
                        <td class="text-right">{{ $currency }}{{ number_format($invoice->rush_fee ?? 0, 2) }}</td>
                    </tr>
                @endif
                </tbody>
            </table>

            <!-- Totals -->
            <div class="flex justify-end mb-12">
                <div class="w-64">
                    <div class="flex justify-between py-2 border-t border-gray-200">
                        <span class="font-semibold">Subtotal:</span>
                        <span>{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                    </div>

                    @if($invoice->rush_enabled_value)
                        <div class="flex justify-between py-2 border-t border-gray-200 font-semibold bg-yellow-50">
                            <span>Rush Add-On ({{ ucfirst($invoice->rush_delivery_type) ?? 'Fast' }}):</span>
                            <span>{{ $currency }}{{ number_format($invoice->rush_fee ?? 0, 2) }}</span>
                        </div>
                    @endif

                    @if($globalSettings->enable_tax)
                        <div class="flex justify-between py-2 border-t border-gray-200">
                            <span class="font-semibold">Tax ({{ $globalSettings->tax_percentage ?? 0 }}%):</span>
                            <span>{{ $currency }}{{ number_format($taxAmount, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between py-3 border-t-2 border-gray-800 text-xl font-bold">
                        <span>Total:</span>
                        <span>{{ $currency }}{{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-8 text-sm">
                @if(!empty($invoice->note))
                    <div class="mb-6">
                        <h3 class="font-bold text-gray-800 mb-2">Project Notes</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $invoice->note }}</p>
                    </div>
                @endif

                @if($globalSettings->enable_invoice_notes && !empty($globalSettings->invoice_notes))
                    <div class="mb-6">
                        <h3 class="font-bold text-gray-800 mb-2">Notes</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $globalSettings->invoice_notes }}</p>
                    </div>
                @endif

                @if($globalSettings->enable_terms && !empty($globalSettings->invoice_terms))
                    <div>
                        <h3 class="font-bold text-gray-800 mb-2">Terms & Conditions</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $globalSettings->invoice_terms }}</p>
                    </div>
                @endif
            </div>

            @include('invoices.partials.activity-log', ['invoice' => $invoice])

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // EDIT BUTTON
        document.getElementById('editInvoiceBtn')?.addEventListener('click', function () {
            window.location.href = "{{ route('invoices.edit', $invoice->id) }}";
        });

        // VOID BUTTON
        document.getElementById('voidBtn')?.addEventListener('click', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will mark the invoice as void.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, void it!',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Voided!', 'Invoice has been voided successfully.', 'success');
                }
            });
        });

        // SEND EMAIL
        document.getElementById('sendEmailBtn').addEventListener('click', function () {
            Swal.fire({
                title: 'Send Invoice Email?',
                text: 'Are you sure you want to send this invoice via email?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, send it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                const btn = document.getElementById('sendEmailBtn');
                btn.disabled = true;
                btn.textContent = 'Sending...';

                fetch('{{ route("invoices.sendEmail", $invoice->id) }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            icon: data.success ? 'success' : 'error',
                            title: data.success ? 'Email Sent!' : 'Error',
                            text: data.message
                        });
                    })
                    .catch(() => {
                        Swal.fire('Error', 'An error occurred while sending the email.', 'error');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-envelope mr-2"></i>Send Email';
                    });
            });
        });

        // ⭐ COPY PUBLIC LINK BUTTON
        document.getElementById('copyLinkBtn').addEventListener('click', function () {
            const publicUrl = "{{ route('invoices.public', $invoice->id) }}";

            navigator.clipboard.writeText(publicUrl)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Link Copied!',
                        text: 'Public URL copied to clipboard.'
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Copy Failed',
                        text: 'Could not copy link.'
                    });
                });
        });

        // DOWNLOAD PDF
        document.getElementById('downloadPdfBtn').addEventListener('click', function () {
            Swal.fire({
                title: 'Download PDF?',
                text: 'Do you want to download this invoice as a PDF?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, download it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (!result.isConfirmed) return;

                const btn = document.getElementById('downloadPdfBtn');
                btn.disabled = true;
                btn.textContent = 'Generating PDF...';

                fetch('{{ route("invoices.download", $invoice->id) }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const link = document.createElement('a');
                            link.href = 'data:application/pdf;base64,' + data.fileData;
                            link.download = data.fileName;
                            link.click();
                            Swal.fire('Success!', data.message, 'success');
                        } else {
                            Swal.fire('Failed!', data.message || 'Failed to generate PDF.', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Error generating PDF. Please try again.', 'error');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-download mr-2"></i>Download PDF';
                    });
            });
        });

    </script>
@endsection
