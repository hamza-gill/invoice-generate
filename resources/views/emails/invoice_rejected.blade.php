@component('mail::message')
    # Invoice Rejected

    The invoice **#{{ $invoice->invoice_number }}** has been **rejected** by the user.

    **User Email:** {{ $invoice->user_email_responded ?? 'N/A' }}
    **Customer Name:** {{ $invoice->customer->name ?? 'N/A' }}

    @component('mail::button', ['url' => route('invoices.show', $invoice->id)])
        View Invoice
    @endcomponent

    Thanks,<br>
    {{ ($globalSettings->company_name ?? config('app.name')) }}
@endcomponent
