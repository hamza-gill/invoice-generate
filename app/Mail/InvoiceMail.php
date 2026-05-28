<?php
namespace App\Mail;

use App\Models\Invoice;
use App\Models\Setting;
use App\Services\InvoiceTemplateRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdfPath;
    public $checkoutUrl;

    public function __construct(Invoice $invoice, $pdfPath, $checkoutUrl)
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
        $this->checkoutUrl = $checkoutUrl;
    }

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

        return $this->subject('Invoice #' . $this->invoice->invoice_number)
            ->view('emails.invoice')
            ->attachData(
                $pdf->output(),
                'invoice-' . $this->invoice->invoice_number . '.pdf',
                [
                    'mime' => 'application/pdf',
                ]
            );
    }
}
