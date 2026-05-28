<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Setting;
use App\Services\InvoiceTemplateRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
class SendInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $this->invoice->load(['customer', 'items.product']);
        $settings = Setting::withoutGlobalScopes()
            ->where('organization_id', $this->invoice->organization_id)
            ->first();

        $renderer = app(InvoiceTemplateRenderer::class);
        $html = $renderer->renderForPdf($this->invoice, $settings);

        $pdf = $html !== ''
            ? Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
            : Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice]);

        return $this->subject('Your Invoice #' . $this->invoice->invoice_number)
            ->view('emails.invoices.sendinvoice')
            ->attachData($pdf->output(), 'invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
