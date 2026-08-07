<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use App\Services\MailConfigurationService;
use App\Mail\InvoiceMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable; // ✅ Add this
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInvoiceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels; // ✅ Include Dispatchable

    protected $invoice;
    protected $pdfPath;
    protected $checkoutUrl;

    public function __construct(Invoice $invoice, $pdfPath, $checkoutUrl)
    {
        $this->invoice = $invoice;
        $this->pdfPath = $pdfPath;
        $this->checkoutUrl = $checkoutUrl;
    }

    public function handle()
    {
        $setting = Setting::withoutGlobalScopes()
            ->where('organization_id', $this->invoice->organization_id)
            ->first();

        app(MailConfigurationService::class)->send(
            $setting,
            $this->invoice->customer->email,
            new InvoiceMail($this->invoice, $this->pdfPath, $this->checkoutUrl)
        );
    }
}
