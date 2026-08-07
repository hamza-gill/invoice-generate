<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Organization;
use App\Models\WebhookSetting;

class SendWebhookNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $event;
    protected array $payload;

    public function __construct(string $event, array $payload)
    {
        $this->event = $event;
        $this->payload = $payload;
    }

    public function handle(): void
    {
        // Resolve which organization (account) this event belongs to.
        $organizationId = $this->payload['organization_id'] ?? null;

        // Each account has its own webhook settings + unique webhook identifier.
        $setting = $organizationId
            ? WebhookSetting::withoutGlobalScopes()->where('organization_id', $organizationId)->first()
            : null;

        // Fall back to a global/legacy setting if the account has no record yet.
        if (!$setting) {
            $setting = WebhookSetting::withoutGlobalScopes()->first();
        }

        if (!$setting || !$setting->webhook_url) {
            Log::warning("Webhook skipped — no webhook URL configured for this account.");
            return;
        }

        // Map event -> setting flag
        $eventMap = [
            'customer.created' => $setting->enable_customer_create,
            'customer.updated' => $setting->enable_customer_update,
            'customer.deleted' => $setting->enable_customer_delete,

            'product.created'  => $setting->enable_product_create,
            'product.updated'  => $setting->enable_product_update,
            'product.deleted'  => $setting->enable_product_delete,

            'invoice.created'  => $setting->enable_invoice_create,
            'invoice.updated'  => $setting->enable_invoice_update,
            'invoice.deleted'  => $setting->enable_invoice_delete,
        ];

        // Skip if event disabled
        if (empty($eventMap[$this->event])) {
            Log::info("Webhook skipped for {$this->event} — event disabled.");
            return;
        }

        // Unique identifier of the account this webhook belongs to.
        $identifier = null;
        if ($organizationId) {
            $identifier = Organization::withoutGlobalScopes()
                ->whereKey($organizationId)
                ->value('webhook_identifier');
        }

        // Payload with identification metadata so the receiver knows which account owns it.
        $payload = $this->payload;
        $payload['webhook'] = [
            'event'                    => $this->event,
            'organization_id'          => $organizationId,
            'organization_identifier'  => $identifier,
            'webhook_url'              => $setting->webhook_url,
            'timestamp'                => now()->toIso8601String(),
        ];

        // Create signature (optional but recommended)
        $signature = hash_hmac('sha256', json_encode($payload), $setting->webhook_secret ?: 'secret');

        try {
            Http::withHeaders([
                'X-Webhook-Event' => $this->event,
                'X-Webhook-Signature' => $signature,
                'X-Webhook-Organization-Id' => $organizationId ? (string) $organizationId : null,
                'X-Webhook-Identifier' => $identifier,
            ])->post($setting->webhook_url, $payload);

            Log::info("Webhook sent successfully for {$this->event}", [
                'organization_id' => $organizationId,
                'url' => $setting->webhook_url,
            ]);
        } catch (\Exception $e) {
            Log::error("Webhook failed for {$this->event}: {$e->getMessage()}", [
                'event' => $this->event,
                'organization_id' => $organizationId,
                'url' => $setting->webhook_url,
            ]);
        }
    }
}
