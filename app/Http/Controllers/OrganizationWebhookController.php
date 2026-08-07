<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Organization;
use App\Models\WebhookSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrganizationWebhookController extends Controller
{
    /**
     * Handle incoming webhooks for a specific account, identified by the unique
     * webhook identifier in the URL, e.g. POST /webhook/{identifier}.
     */
    public function handle(Request $request, string $identifier)
    {
        $organization = Organization::withoutGlobalScopes()
            ->where('webhook_identifier', $identifier)
            ->first();

        if (!$organization) {
            Log::warning('Webhook received for unknown identifier.', ['identifier' => $identifier]);
            return response('Unknown webhook identifier', 404);
        }

        $setting = WebhookSetting::withoutGlobalScopes()
            ->where('organization_id', $organization->id)
            ->first();

        // Verify the signature using the account's webhook secret (when configured).
        if ($setting && $setting->webhook_secret) {
            $signature = $request->header('X-Inveqi-Signature')
                ?? $request->header('X-Webhook-Signature');

            $computed = hash_hmac('sha256', $request->getContent(), $setting->webhook_secret);

            if (!$signature || !hash_equals($computed, $signature)) {
                Log::warning('Account webhook signature verification failed.', [
                    'organization_id' => $organization->id,
                ]);
                return response('Invalid signature', 401);
            }
        } elseif (!$setting || !$setting->webhook_secret) {
            Log::warning('Webhook secret not configured for account.', [
                'organization_id' => $organization->id,
            ]);
            return response('Webhook secret not configured', 503);
        }

        $payload = $request->all();

        $event = $payload['event'] ?? $payload['type'] ?? 'unknown';

        Log::info("Account webhook received: {$event}", [
            'organization_id' => $organization->id,
        ]);

        $this->handleEvent($event, $payload, $organization);

        return response('Webhook received', 200);
    }

    /**
     * Handle the event the same way the existing webhook handling works.
     */
    protected function handleEvent(string $event, array $payload, Organization $organization): void
    {
        $data = $payload['data'] ?? $payload;

        switch ($event) {
            case 'invoice.paid':
            case 'checkout.session.completed':
                $this->updateInvoice($data, $organization, 'paid');
                break;

            case 'invoice.pending':
                $this->updateInvoice($data, $organization, 'pending');
                break;

            default:
                Log::info('Unhandled account webhook event.', [
                    'event' => $event,
                    'organization_id' => $organization->id,
                ]);
        }
    }

    /**
     * Locate the invoice within the account and update its payment status.
     */
    protected function updateInvoice(array $data, Organization $organization, string $status): void
    {
        $invoiceId = $data['invoice_id'] ?? $data['invoice'] ?? ($data['id'] ?? null);
        $transactionId = $data['gateway_transaction_id'] ?? ($data['transaction_id'] ?? null);
        $paymentStatus = $status === 'paid' ? 'paid' : ($data['payment_status'] ?? $status);

        $query = Invoice::withoutGlobalScopes()->where('organization_id', $organization->id);

        $invoice = null;
        if ($invoiceId) {
            $invoice = $query->clone()->whereKey($invoiceId)->first();
        }
        if (!$invoice && $transactionId) {
            $invoice = $query->clone()->where('gateway_transaction_id', $transactionId)->first();
        }

        if ($invoice) {
            $invoice->update([
                'payment_status' => $paymentStatus,
                'status'         => $status,
                'gateway_transaction_id' => $transactionId ?: $invoice->gateway_transaction_id,
                'gateway_response' => array_merge($invoice->gateway_response ?? [], $data),
            ]);

            Log::info("Invoice marked {$status} via account webhook.", [
                'invoice_id'      => $invoice->id,
                'organization_id' => $organization->id,
            ]);
        } else {
            Log::warning('No matching invoice found for account webhook.', [
                'organization_id' => $organization->id,
            ]);
        }
    }
}
