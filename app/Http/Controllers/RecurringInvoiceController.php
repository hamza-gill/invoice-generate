<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\RecurringInvoice;
use App\Models\Setting;
use App\Services\InvoiceTemplateRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecurringInvoiceController extends Controller
{
    public function index()
    {
        $recurringInvoices = RecurringInvoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('recurring.index', compact('recurringInvoices'));
    }

    public function create()
    {
        $customers = Customer::latest()->get();
        return view('recurring.create', compact('customers'));
    }

    /**
     * Render a live preview of the FIRST invoice this recurring schedule would
     * generate, using unsaved form data — same idea as InvoiceController::preview(),
     * since a recurring invoice's whole purpose is to produce real Invoice records.
     */
    public function preview(Request $request, InvoiceTemplateRenderer $renderer)
    {
        $customer = new Customer([
            'first_name'   => $request->input('first_name'),
            'last_name'    => $request->input('last_name'),
            'company_name' => $request->input('company_name'),
            'email'        => $request->input('email'),
            'phone_number' => $request->input('phone_number'),
            'address'      => $request->input('address'),
            'city'         => $request->input('city'),
            'state'        => $request->input('state'),
            'postal_code'  => $request->input('postal_code'),
            'country'      => $request->input('country', 'USA'),
        ]);

        $startDate = $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : now();

        $invoice = new Invoice([
            'invoice_number'  => 'PREVIEW',
            'description'     => $request->input('title') ?? '',
            'issue_date'      => $startDate,
            'due_date'        => $startDate->copy()->addDays(30),
            'note'            => $request->input('notes') ?? '',
            'project_address' => $request->input('project_address') ?? '',
            'discount'        => $request->input('discount', 0),
            'status'          => 'sent',
        ]);
        $invoice->organization_id = Auth::user()->organization_id ?? null;
        $invoice->setRelation('customer', $customer);

        $lineItems = collect($request->input('line_items', []))->map(function ($item) {
            $quantity  = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $product   = !empty($item['product_id']) ? Product::find($item['product_id']) : null;

            $invoiceItem = new InvoiceItem([
                'activity'   => $item['description'] ?? ($product->name ?? ''),
                'product_id' => $item['product_id'] ?? null,
                'quantity'   => $quantity,
                'amount'     => $unitPrice,
                'total'      => $quantity * $unitPrice,
            ]);

            return $invoiceItem->setRelation('product', $product);
        });

        $invoice->setRelation('items', $lineItems);

        $settings = Setting::withoutGlobalScopes()
            ->where('organization_id', Auth::user()->organization_id)
            ->first();

        $subtotal = $lineItems->sum(fn ($item) => $item->quantity * $item->amount);
        $discount = (float) $request->input('discount', 0);
        $taxRate  = ($settings->enable_tax ?? false) ? (($settings->tax_percentage ?? 0) / 100) : 0;

        $invoice->subtotal  = $subtotal;
        $invoice->taxAmount = $subtotal * $taxRate;
        $invoice->total     = max(0, $subtotal + $invoice->taxAmount - $discount);
        $invoice->amount    = $invoice->total;

        $html = $renderer->render($invoice, $settings);

        if ($html === '') {
            $html = view('invoices.pdf', compact('invoice'))->render();
        }

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'frequency' => 'required|in:weekly,biweekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_occurrences' => 'nullable|integer|min:1',
            'auto_send_email' => 'nullable',
            'line_items' => 'required|array|min:1',
            'line_items.*.product_id' => 'required|exists:products,id',
            'line_items.*.description' => 'required|string',
            'line_items.*.quantity' => 'required|numeric|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'project_address' => 'nullable|string',
        ]);

        // Fetch or create the customer — mirrors InvoiceController::store(), and
        // supports the "+ Add New Customer" dropdown option in the blade form.
        $customer = Customer::updateOrCreate(
            ['email' => $request->input('email')],
            [
                'first_name'   => $request->input('first_name'),
                'last_name'    => $request->input('last_name'),
                'company_name' => $request->input('company_name'),
                'address'      => $request->input('address'),
                'city'         => $request->input('city'),
                'phone_number' => $request->input('phone_number'),
                'country'      => $request->input('country', 'USA'),
                'state'        => $request->input('state'),
                'postal_code'  => $request->input('postal_code'),
            ]
        );

        $lineItems = $request->input('line_items', []);
        $subtotal = collect($lineItems)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
        $discount = floatval($request->input('discount', 0));

        RecurringInvoice::create([
            'user_id' => Auth::id(),
            'customer_id' => $customer->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'amount' => max(0, $subtotal - $discount),
            'discount' => $discount,
            'frequency' => $request->input('frequency'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'next_send_date' => $request->input('start_date'),
            'max_occurrences' => $request->input('max_occurrences'),
            'status' => 'active',
            'line_items' => $lineItems,
            'notes' => $request->input('notes'),
            'project_address' => $request->input('project_address'),
            'auto_send_email' => $request->boolean('auto_send_email', true),
        ]);

        return redirect()->route('recurring.index')
            ->with('success', 'Recurring invoice created successfully.');
    }

    public function show(RecurringInvoice $recurring)
    {
        $recurring->load(['customer', 'invoices.customer']);

        $subtotal = collect($recurring->line_items ?? [])->sum(fn($i) => ($i['quantity'] ?? 0) * ($i['unit_price'] ?? 0));
        $discount = (float) ($recurring->discount ?? 0);
        $total = max(0, $subtotal - $discount);

        return view('recurring.show', compact('recurring', 'subtotal', 'discount', 'total'));
    }

    public function edit(RecurringInvoice $recurring)
    {
        $customers = Customer::latest()->get();
        return view('recurring.edit', compact('recurring', 'customers'));
    }

    public function update(Request $request, RecurringInvoice $recurring)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'frequency' => 'required|in:weekly,biweekly,monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_occurrences' => 'nullable|integer|min:1',
            'auto_send_email' => 'nullable',
            'line_items' => 'required|array|min:1',
            'line_items.*.product_id' => 'required|exists:products,id',
            'line_items.*.description' => 'required|string',
            'line_items.*.quantity' => 'required|numeric|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'project_address' => 'nullable|string',
        ]);

        $lineItems = $request->input('line_items', []);
        $subtotal = collect($lineItems)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
        $discount = floatval($request->input('discount', 0));

        $recurring->update([
            'customer_id' => $request->input('customer_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'amount' => max(0, $subtotal - $discount),
            'discount' => $discount,
            'frequency' => $request->input('frequency'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'max_occurrences' => $request->input('max_occurrences'),
            'line_items' => $lineItems,
            'notes' => $request->input('notes'),
            'project_address' => $request->input('project_address'),
            'auto_send_email' => $request->boolean('auto_send_email', true),
        ]);

        return redirect()->route('recurring.show', $recurring)
            ->with('success', 'Recurring invoice updated.');
    }

    public function pause(RecurringInvoice $recurring)
    {
        $recurring->update(['status' => 'paused']);
        return back()->with('success', 'Recurring invoice paused.');
    }

    public function resume(RecurringInvoice $recurring)
    {
        $nextDate = $recurring->calculateNextSendDate();
        $recurring->update([
            'status' => 'active',
            'next_send_date' => $nextDate ?? now()->toDateString(),
        ]);
        return back()->with('success', 'Recurring invoice resumed.');
    }

    public function clone(RecurringInvoice $recurring)
    {
        $clone = $recurring->replicate();
        $clone->title = '[Clone] ' . $recurring->title;
        $clone->status = 'paused';
        $clone->total_sent = 0;
        $clone->last_sent_at = null;
        $clone->next_send_date = $recurring->start_date;
        $clone->save();

        return redirect()->route('recurring.edit', $clone)
            ->with('success', 'Recurring invoice cloned. Edit and activate when ready.');
    }

    public function destroy(RecurringInvoice $recurring)
    {
        $recurring->update(['status' => 'cancelled']);
        return redirect()->route('recurring.index')
            ->with('success', 'Recurring invoice cancelled.');
    }
}
