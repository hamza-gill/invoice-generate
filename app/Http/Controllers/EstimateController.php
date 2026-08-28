<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Setting;
use Carbon\Carbon;
use App\Services\InvoiceTemplateRenderer;
use App\Services\InvoiceTemplateTheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstimateController extends Controller
{
    public function index()
    {
        $estimates = Estimate::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('estimates.index', compact('estimates'));
    }

    public function create()
    {
        $customers = Customer::latest()->get();
        $estimateNumber = Estimate::generateEstimateNumber();
        return view('estimates.create', compact('customers', 'estimateNumber'));
    }
    /**
     * 2. Add this new method anywhere inside the EstimateController class
     *    (placed here right after create(), to mirror InvoiceController::preview()).
     */

    /**
     * Render a live preview of the estimate using unsaved form data,
     * so the user can see the result before actually creating it.
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

        $issueDate  = $request->filled('issue_date') ? Carbon::parse($request->input('issue_date')) : now();
        $validUntil = $request->filled('valid_until') ? Carbon::parse($request->input('valid_until')) : null;

        $estimate = new Estimate([
            'estimate_number' => $request->input('estimate_number') ?: 'PREVIEW',
            'description'     => $request->input('description') ?? '',
            'issue_date'      => $issueDate,
            'valid_until'     => $validUntil,
            'notes'           => $request->input('notes') ?? '',
            'project_address' => $request->input('project_address') ?? '',
            'discount'        => $request->input('discount', 0),
            'status'          => 'draft',
        ]);
        $estimate->organization_id = Auth::user()->organization_id ?? null;
        $estimate->setRelation('customer', $customer);

        $lineItems = collect($request->input('line_items', []))->map(function ($item) {
            $quantity  = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $product   = !empty($item['product_id']) ? Product::find($item['product_id']) : null;

            $estimateItem = new EstimateItem([
                'activity'    => $item['description'] ?? ($product->name ?? ''),
                'description' => $item['description'] ?? ($product->name ?? ''),
                'product_id'  => $item['product_id'] ?? null,
                'quantity'    => $quantity,
                'amount'      => $unitPrice,
                'total'       => $quantity * $unitPrice,
            ]);

            return $estimateItem->setRelation('product', $product);
        });

        $estimate->setRelation('items', $lineItems);

        $settings = Setting::withoutGlobalScopes()
            ->where('organization_id', Auth::user()->organization_id)
            ->first();

        $subtotal = $lineItems->sum(fn ($item) => $item->quantity * $item->amount);
        $discount = (float) $request->input('discount', 0);

        $estimate->subtotal = $subtotal;
        $estimate->total    = max(0, $subtotal - $discount);
        $estimate->amount   = $estimate->total;

        $html = $renderer->renderEstimate($estimate, $settings);

        if ($html === '') {
            $html = '<div style="font-family: sans-serif; padding: 3rem; color:#64748b; text-align:center;">'
                . 'No estimate template is configured yet — this preview has nothing to render.'
                . '</div>';
        }

        return response()->json([
            'success' => true,
            'html'    => $html,
        ]);
    }

    /**
     * 3. REPLACE your entire store() method with this. It now creates/updates the
     *    customer from the posted fields (keyed on email) instead of requiring an
     *    existing customer_id — matching InvoiceController::store() and supporting
     *    the new "+ Add New Customer" dropdown option in the blade form.
     */

    public function store(Request $request)
    {
        $request->validate([
            // customer_id is no longer required from the client — we resolve/create
            // the customer ourselves below, the same way InvoiceController::store() does.
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'issue_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:issue_date',
            'line_items' => 'required|array|min:1',
            'line_items.*.product_id' => 'required|exists:products,id',
            'line_items.*.description' => 'required|string',
            'line_items.*.quantity' => 'required|numeric|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'project_address' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Fetch or create the customer — mirrors InvoiceController::store().
            // Works whether the dropdown had an existing customer selected (their
            // fields were populated automatically) or "+ Add New Customer" was chosen.
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

            $customFields = $request->input('custom_fields')
                ? array_values(array_filter($request->input('custom_fields'), fn($f) => !empty($f['label'])))
                : null;

            $status = $request->input('action') === 'send' ? 'sent' : 'draft';

            $estimate = Estimate::create([
                'user_id' => Auth::id(),
                'customer_id' => $customer->id,
                'estimate_number' => $request->input('estimate_number') ?: Estimate::generateEstimateNumber(),
                'description' => $request->input('description'),
                'amount' => max(0, $subtotal - $discount),
                'discount' => $discount,
                'issue_date' => $request->input('issue_date'),
                'valid_until' => $request->input('valid_until'),
                'status' => $status,
                'notes' => $request->input('notes'),
                'project_address' => $request->input('project_address'),
                'custom_fields' => $customFields,
            ]);

            foreach ($lineItems as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $estimate->items()->create([
                    'product_id' => $item['product_id'],
                    'activity' => $item['description'],
                    'description' => $item['description'],
                    'amount' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total' => $lineTotal,
                ]);
            }

            DB::commit();
            return redirect()->route('estimates.show', $estimate)
                ->with('success', 'Estimate created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to create estimate: ' . $e->getMessage()]);
        }
    }
    public function show(Estimate $estimate)
    {
        $estimate->load(['customer', 'items.product', 'convertedInvoice']);
        $statuses = ['draft', 'sent', 'viewed', 'approved', 'converted'];

        return view('estimates.show', compact('estimate', 'statuses'))
            ->with('hideNavbar', true);
    }

    public function edit(Estimate $estimate)
    {
        $estimate->load('items.product');
        $customers = Customer::latest()->get();
        return view('estimates.edit', compact('estimate', 'customers'));
    }

    public function update(Request $request, Estimate $estimate)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:issue_date',
            'line_items' => 'required|array|min:1',
            'line_items.*.product_id' => 'required|exists:products,id',
            'line_items.*.description' => 'required|string',
            'line_items.*.quantity' => 'required|numeric|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $lineItems = $request->input('line_items', []);
            $subtotal = collect($lineItems)->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $discount = floatval($request->input('discount', 0));

            $customFields = $request->input('custom_fields')
                ? array_values(array_filter($request->input('custom_fields'), fn($f) => !empty($f['label'])))
                : null;

            $estimate->update([
                'customer_id' => $request->input('customer_id'),
                'description' => $request->input('description'),
                'amount' => max(0, $subtotal - $discount),
                'discount' => $discount,
                'issue_date' => $request->input('issue_date'),
                'valid_until' => $request->input('valid_until'),
                'notes' => $request->input('notes'),
                'project_address' => $request->input('project_address'),
                'custom_fields' => $customFields,
            ]);

            $estimate->items()->delete();
            foreach ($lineItems as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $estimate->items()->create([
                    'product_id' => $item['product_id'],
                    'activity' => $item['description'],
                    'description' => $item['description'],
                    'amount' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total' => $lineTotal,
                ]);
            }

            DB::commit();
            return redirect()->route('estimates.show', $estimate)
                ->with('success', 'Estimate updated.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                ->withErrors(['error' => 'Failed to update estimate.']);
        }
    }

    public function send(Estimate $estimate)
    {
        $estimate->update(['status' => 'sent']);
        return back()->with('success', 'Estimate marked as sent. Share the public link with your client.');
    }

    public function publicView(string $token, InvoiceTemplateRenderer $renderer, InvoiceTemplateTheme $themeService)
    {
        $estimate = $this->resolvePublicEstimate($token);

        if ($estimate->status === 'sent') {
            $estimate->update(['status' => 'viewed']);
        }

        $globalSettings = Setting::withoutGlobalScopes()
            ->where('organization_id', $estimate->organization_id)
            ->first();

        $html = $renderer->renderEstimate($estimate, $globalSettings);
        $invoiceTemplate = $renderer->resolveForEstimate($estimate);
        $templateTheme = $themeService->forTemplate($invoiceTemplate);

        return view('estimates.public', [
            'estimate' => $estimate,
            'globalSettings' => $globalSettings,
            'publicKey' => $token,
            'invoiceDocumentHtml' => $html,
            'invoiceDocumentSrcdoc' => $html !== ''
                ? htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                : '',
            'invoiceTemplate' => $invoiceTemplate,
            'templateTheme' => $templateTheme,
        ]);
    }

    public function approve(string $token)
    {
        $estimate = $this->resolvePublicEstimate($token);

        if (!$estimate->canBeApproved()) {
            return back()->with('error', 'This estimate can no longer be approved.');
        }

        $estimate->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Estimate approved! The provider will be notified.');
    }

    public function decline(Request $request, string $token)
    {
        $estimate = $this->resolvePublicEstimate($token);

        $estimate->update([
            'status' => 'declined',
            'declined_at' => now(),
        ]);

        return back()->with('success', 'Estimate declined.');
    }

    public function convertToInvoice(Estimate $estimate)
    {
        if ($estimate->status !== 'approved') {
            return back()->with('error', 'Only approved estimates can be converted to invoices.');
        }

        DB::beginTransaction();
        try {
            $invoiceNumber = Invoice::consumeNextInvoiceNumber();
            $issueDate = Carbon::now();

            $invoice = Invoice::create([
                'user_id' => Auth::id(),
                'customer_id' => $estimate->customer_id,
                'invoice_number' => $invoiceNumber,
                'description' => $estimate->description ?? '',
                'amount' => $estimate->amount,
                'status' => 'sent',
                'issue_date' => $issueDate,
                'due_date' => $issueDate->copy()->addDays(30),
                'note' => $estimate->notes ?? '',
                'project_address' => $estimate->project_address ?? '',
                'discount' => $estimate->discount,
                'estimate_id' => $estimate->id,
                'custom_fields' => $estimate->custom_fields,
                'invoice_template_id' => $estimate->invoice_template_id,
            ]);

            foreach ($estimate->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'activity' => $item->activity,
                    'description' => $item->description,
                    'amount' => $item->amount,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                ]);
            }

            $estimate->update([
                'status' => 'converted',
                'converted_invoice_id' => $invoice->id,
            ]);

            DB::commit();
            return redirect()->route('invoices.show', $invoice)
                ->with('success', "Estimate converted to Invoice #{$invoiceNumber}.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to convert estimate: ' . $e->getMessage());
        }
    }

    public function destroy(Estimate $estimate)
    {
        if (!in_array($estimate->status, ['draft', 'declined'])) {
            return back()->with('error', 'Only draft or declined estimates can be deleted.');
        }

        $estimate->delete();
        return redirect()->route('estimates.index')
            ->with('success', 'Estimate deleted.');
    }

    protected function resolvePublicEstimate(string $key): Estimate
    {
        $query = Estimate::withoutGlobalScopes()
            ->with(['customer', 'items.product']);

        $estimate = ctype_digit($key)
            ? $query->where('id', (int) $key)->first()
            : $query->where('client_token', $key)->first();

        if (!$estimate) {
            abort(404);
        }

        if (!$estimate->client_token) {
            $estimate->forceFill(['client_token' => Str::random(64)])->save();
        }

        return $estimate;
    }
}
