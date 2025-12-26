<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceRejectedMail;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class InvoiceResponseController extends Controller
{
    public function respond(Request $request, Invoice $invoice)
    {
        $action = $request->query('action');

        // Mark user as responded
        $invoice->user_responded = now();
        $invoice->save();

        // ✅ 1. Already rejected
        if ($invoice->status === 'declined') {
            $invoice->logActivity('viewed_rejected', 'Customer viewed already rejected invoice.');
            $invoice->notifyAction('viewed_rejected', 'Customer viewed already rejected invoice.', 'declined');

            return view('invoices.rejected', compact('invoice'));
        }

        // ✅ 2. Already paid
        if (in_array($invoice->payment_status, ['paid', 'completed'])) {
            $invoice->logActivity('viewed_paid', 'Customer viewed already paid invoice.');
            $invoice->notifyAction('viewed_paid', 'Customer viewed already paid invoice.', 'viewed');

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'This invoice has already been paid.');
        }

        // ✅ 3. Accept action → redirect to review page
        if ($action === 'accept') {
            $invoice->logActivity('viewed_accept_page', 'Customer viewed acceptance review page before payment.');
            $invoice->notifyAction('viewed_accept_page', 'Customer viewed acceptance review page.', 'accepting');

            return redirect()->route('invoices.accept.page', $invoice->id);
        }

        // ✅ 4. Reject action
        if ($action === 'declined') {
            if ($invoice->status !== 'declined') {
                $invoice->status = 'declined';
                $invoice->save();

                $invoice->logActivity('rejected', 'Customer rejected the invoice.');
                $invoice->notifyAction('rejected', 'Customer rejected the invoice.', 'rejected');

                // Send rejection email
                Mail::to('hamzagill451@gmail.com')->send(new InvoiceRejectedMail($invoice));
            } else {
                $invoice->logActivity('viewed_rejected', 'Customer viewed already rejected invoice.');
                $invoice->notifyAction('viewed_rejected', 'Customer viewed already rejected invoice.', 'viewed_rejected');
            }

            return view('invoices.rejected', compact('invoice'));
        }

        // 5. Fallback for invalid actions
        $invoice->logActivity('invalid_action', "Customer tried invalid invoice action: {$action}");
        $invoice->notifyAction('invalid_action', "Customer tried invalid invoice action: {$action}", 'invalid_action');

        abort(404, 'Invalid action.');
    }

    //  STEP 1: Review & accept page before payment
    public function acceptPage(Invoice $invoice)
    {
        if ($invoice->status === 'declined') {
            return view('invoices.rejected', compact('invoice'));
        }

        if (in_array($invoice->payment_status, ['paid', 'completed'])) {
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'This invoice has already been paid.');
        }

        $invoice->logActivity('viewed_accept_page', 'Customer viewed invoice acceptance page.');
        $invoice->notifyAction('viewed_accept_page', 'Customer viewed invoice acceptance page.', 'accept_page');

        return view('invoices.accept', compact('invoice'));
    }

    // ✅ STEP 2: Create Stripe session (includes rush fee if enabled)
    public function createPaymentSession(Request $request, Invoice $invoice)
    {
        // Subtotal
        $subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->amount);

        // Rush
        $rushEnabled = $request->input('rush_enabled_value', 0);
        $rushFee = $rushEnabled ? floatval($request->input('rush_fee', 0)) : 0;

        // Discount
        $discount = floatval($invoice->discount ?? 0);

        // Tax
        $globalSettings = config('settings'); // or however you access it


        if (!empty($globalSettings['tax_percentage']) && $globalSettings['tax_percentage']) {

            $taxRate = ($globalSettings['tax_percentage'] ?? 0) / 100;
            $taxAmount = round((($subtotal + $rushFee) * $taxRate),2);

        } else {
            $taxAmount = 0;
        }

        // Final total
        $totalAmount = max(
            0,
            $subtotal + $rushFee + $taxAmount - $discount
        );

        // Stripe
        $stripeSecret = config('settings.stripe_secret_key');
        if (!$stripeSecret) abort(500, 'Stripe secret key not configured.');
        Stripe::setApiKey($stripeSecret);

        $lineItems = [
            [
                'price_data' => [
                    'currency' => 'USD',
                    'product_data' => [
                        'name' => "Invoice #{$invoice->invoice_number}"
                    ],
                    'unit_amount' => (int) round($totalAmount * 100),
                ],
                'quantity' => 1,
            ],
        ];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'customer_email' => $invoice->customer->email,
            'success_url' => route('invoices.success', $invoice->id),
            'cancel_url' => route('invoices.cancel', $invoice->id),
            'metadata' => [
                'invoice_id' => $invoice->id,
            ],
        ]);

        // Save Stripe + invoice values
        $invoice->update([
            'payment_gateway'        => 'stripe',
            'gateway_transaction_id' => $session->id,
            'gateway_response'       => array_merge($session->toArray(), ['url' => $session->url]),
            'payment_status'         => 'pending',
            'status'                 => 'pending',

            // Amounts
            'amount'                 => $totalAmount,
            'tax_amount'             => $taxAmount,

            // Rush
            'rush_enabled_value'     => $rushEnabled,
            'rush_description'       => $request->input('rush_label'),
            'rush_fee'               => $rushFee,
            'rush_delivery_type'     => $request->input('rush_delivery_days', 'standard'),
        ]);

        $invoice->logActivity(
            'proceed_payment',
            'Customer proceeded to payment via Stripe checkout.'
        );

        $invoice->notifyAction(
            'proceed_payment',
            'Customer proceeded to Stripe checkout.',
            'pending'
        );

        return redirect($session->url);
    }



    public function rejected(Invoice $invoice)
    {
        $invoice->logActivity('viewed_rejected_page', 'Customer viewed rejected confirmation page.');
        $invoice->notifyAction('viewed_rejected_page', 'Customer viewed rejected confirmation page.', 'viewed');

        return view('invoices.rejected', compact('invoice'));
    }

    public function success(Invoice $invoice)
    {
        $invoice->update([
            'payment_status' => 'paid',
            'status' => 'paid',
        ]);

        $invoice->logActivity('payment_success', 'Customer completed payment successfully.');
        $invoice->notifyAction('payment_success', 'Customer completed payment successfully.', 'paid');

        return view('invoices.success', compact('invoice'));
    }

    public function cancel(Invoice $invoice)
    {
        $invoice->logActivity('payment_cancel', 'Customer canceled payment on Stripe.');
        $invoice->notifyAction('payment_cancel', 'Customer canceled payment on Stripe.', 'cancelled');

        return view('invoices.cancel', compact('invoice'));
    }
}
