<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Setting;
use App\Services\InvoiceTemplateRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Number of days the invoice is overdue.
     *
     * @var int
     */
    public $overdueDays;

    /**
     * Org settings (exposed so the view works outside an HTTP request).
     *
     * @var \App\Models\Setting|null
     */
    public $globalSettings;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, int $overdueDays, ?Setting $setting = null)
    {
        $this->invoice = $invoice;
        $this->overdueDays = $overdueDays;
        $this->globalSettings = $setting;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $this->invoice->load(['customer', 'items.product']);
        $setting = $this->globalSettings ?? Setting::withoutGlobalScopes()
            ->where('organization_id', $this->invoice->organization_id)
            ->first();

        $renderer = app(InvoiceTemplateRenderer::class);
        $html = $renderer->renderForPdf($this->invoice, $setting);

        $pdf = $html !== ''
            ? Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setOption('isHtml5ParserEnabled', true)
                ->setOption('defaultFont', 'DejaVu Sans')
            : Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice]);

        return $this->subject('Reminder: Payment Due for Invoice #' . $this->invoice->invoice_number)
            ->view('emails.invoice-reminder')
            ->attachData($pdf->output(), 'invoice-' . $this->invoice->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
