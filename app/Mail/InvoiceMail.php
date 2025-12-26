<?php
namespace App\Mail;

use App\Models\Invoice;
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
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $this->invoice
        ]);

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
