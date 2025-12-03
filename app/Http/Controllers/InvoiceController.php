<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Mail\SendInvoiceMail;
use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Jobs\SendInvoiceEmail;
use Stripe\StripeClient;

class InvoiceController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view', Invoice::class);

        $invoices = Invoice::with('customer:id,name,company_name')->orderBy('id','desc')->paginate(10);

        return view('invoices.index', compact('invoices'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Invoice::class);
        $customers = Customer::latest()->first();
        return view('invoices.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */


    public function store(StoreInvoiceRequest $request)
    {
        $this->authorize('create', Invoice::class);
        DB::beginTransaction();

        try {
            //  Fetch or create customer
            $customer = Customer::updateOrCreate(
                ['email' => $request->input('email')],
                [
                    'name' => $request->input('name'),
                    'address' => $request->input('address'),
                    'company_name' => $request->input('company_name'),
                    'city' => $request->input('city'),
                    'country' => 'USA',
                ]
            );

            //  Generate or use provided invoice number
            $invoiceNumber = $request->input('invoice_number') ?: Invoice::consumeNextInvoiceNumber();

            //  Calculate due date — 1 year after issue date
            $issueDate = Carbon::parse($request->input('issue_date'));
            $dueDate = $issueDate->copy()->addYear();

            //  Create invoice (excluding rush fee from total)
            $invoice = Invoice::create([
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
                'invoice_number' => $invoiceNumber,
                'description' => $request->input('description') ?? '',
                'amount' => 0,
                'status' => 'sent',
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'note' => $request->input('notes') ?? '',
                'project_address' => $request->input('project_address') ?? '',

                // Rush Add-On fields
                'enable_rush_addon' => (bool) $request->input('enable_rush_addon', false),
                'rush_delivery_type' => $request->input('rush_delivery_type'),
                'rush_description' => $request->input('rush_description'),
                'rush_fee' => $request->input('rush_fee'),
            ]);
            $invoice->consumeNextInvoiceNumber();
            //  Create line items & calculate subtotal
            $totalAmount = 0;
            foreach ($request->input('line_items', []) as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];

                $invoice->items()->create([
                    'activity' => $item['description'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);

                $totalAmount += $lineTotal;
            }

            //  Update invoice total (rush fee excluded for now)
            $invoice->update(['amount' => $totalAmount]);

            $invoice->logActivity( 'created',
                sprintf(
                    'Invoice #%s created by %s for customer %s, email sent to %s.',
                    $invoice->invoice_number,
                    auth()->user()->name ?? 'System',
                    $customer->name,
                    $customer->email
                )
            );

            // ✅ Send invoice email
            SendInvoiceEmail::dispatchSync($invoice, '', '');

            DB::commit();

            return redirect()
                ->route('invoices.index')
                ->with('success', 'Invoice created successfully!');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while creating the invoice. Please try again.']);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['customer', 'items', 'activities']);

        $invoice->subtotal = $invoice->items->sum(fn($item) => $item->quantity * $item->unit_price);
        $invoice->taxRate = 0.1;
        $invoice->taxAmount = $invoice->subtotal * $invoice->taxRate;
        $invoice->total = $invoice->subtotal + $invoice->taxAmount;

        $invoice->status_color = match ($invoice->status) {
            'sent' => 'bg-blue-100 text-blue-600',
            'paid' => 'bg-green-100 text-green-600',
            'void' => 'bg-red-100 text-red-600',
            default => 'bg-gray-100 text-gray-600',
        };

        return view('invoices.show', compact('invoice'));
    }


    public function search(Request $request)
    {
        try {
            $this->authorize('view', Invoice::class);

            $query = $request->input('query');

            $invoices = Invoice::with('customer')
                ->when($query, function ($q) use ($query) {
                    $q->where('invoice_number', 'like', "%{$query}%")
                        ->orWhereHas('customer', function ($q2) use ($query) {
                            $q2->where('name', 'like', "%{$query}%");
                        })
                        ->orWhere('status', 'like', "%{$query}%");
                })
                ->orderBy('issue_date', 'desc')
                ->limit(25)
                ->get();

            $data = $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_name' => $invoice->customer->name ?? 'N/A',
                    'amount' => number_format($invoice->amount, 2),
                    'status' => $invoice->status,
                    'issue_date_formatted' => optional($invoice->issue_date)->format('M d, Y'),
                    'due_date_formatted' => optional($invoice->due_date)->format('M d, Y'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Invoice search failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'query' => $request->input('query'),
            ]);

            // Return a JSON error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching invoices.',
                'error' => config('app.debug') ? $e->getMessage() : null, // Optional: show detailed error only in debug mode
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $customers = Customer::latest()->get();
        return view('invoices.edit', compact('customers','invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'invoice_number' => 'nullable|string',
            'issue_date' => 'required|date',
            'line_items.*.description' => 'required|string',
            'line_items.*.product_id' => 'required|integer|exists:products,id',
            'line_items.*.quantity' => 'required|numeric|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Fetch or update customer
            $customer = Customer::updateOrCreate(
                ['email' => $request->input('email')],
                [
                    'name' => $request->input('name'),
                    'address' => $request->input('address'),
                    'company_name' => $request->input('company_name'),
                    'city' => $request->input('city'),
                    'country' => 'USA',
                ]
            );

            // ✅ Recalculate dates
            $issueDate = Carbon::parse($request->input('issue_date'));
            $dueDate = $issueDate->copy()->addYear();

            // ✅ Update main invoice info
            $invoice->update([
                'customer_id' => $customer->id,
                'invoice_number' => $request->input('invoice_number') ?: $invoice->invoice_number,
                'description' => $request->input('description') ?? '',
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'note' => $request->input('notes') ?? '',
                'project_address' => $request->input('project_address') ?? '',
                'status' => $invoice->status, // keep status unless you want to modify
            ]);

            // ✅ Remove old line items before adding updated ones
            $invoice->items()->delete();

            // ✅ Recreate all line items and calculate total
            $totalAmount = 0;
            foreach ($request->input('line_items', []) as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];

                $invoice->items()->create([
                    'activity' => $item['description'],
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'amount' => $item['unit_price'],
                    'total' => $lineTotal,
                ]);

                $totalAmount += $lineTotal;
            }

            // ✅ Update invoice total
            $invoice->update(['amount' => $totalAmount]);

            // ✅ Log activity
            $invoice->logActivity(
                'updated',
                "Invoice #{$invoice->invoice_number} updated by " . (auth()->user()->name ?? 'System') . "."
            );

            DB::commit();

            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Invoice update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while updating the invoice. Please try again.']);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        //
    }

    /**
     * Download invoice as PDF.
     */

    public function downloadPdf(Invoice $invoice, Request $request)
    {

        try {

            // Generate PDF from the invoice Blade view
            $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
            $invoice->logActivity(
                'downloaded',
                'Invoice PDF downloaded by ' . (auth()->user()->name ?? 'Guest') . '.'
            );
            // If AJAX request, return base64-encoded PDF
            if ($request->ajax()) {
                $pdfContent = $pdf->output();
                $fileName = 'invoice-' . $invoice->invoice_number . '.pdf';

                return response()->json([
                    'success' => true,
                    'fileName' => $fileName,
                    'fileData' => base64_encode($pdfContent),
                    'message' => 'Invoice PDF generated successfully.'
                ]);
            }

            // Otherwise, normal download
            return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send invoice email with PDF attachment.
     */
    public function sendInvoiceEmail($id)
    {
        $invoice = Invoice::with(['customer', 'items'])->findOrFail($id);
        $this->authorize('send', $invoice);
        try {
            Mail::to($invoice->customer->email)->send(new SendInvoiceMail($invoice));
            $invoice->logActivity(
                'email_sent',
                'Invoice email sent to ' . $invoice->customer->email . ' by ' . (auth()->user()->name ?? 'System') . '.'
            );
            return response()->json([
                'success' => true,
                'message' => 'Invoice email sent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invoice email: ' . $e->getMessage()
            ], 500);
        }
    }


    public function public(Invoice $invoice)
    {
        return view('invoices.public', compact('invoice'));
    }

    /**
     * Void invoice (and optionally cancel on Stripe).
     */
    public function void(Invoice $invoice)
    {
        $this->authorize('void', $invoice);
        $invoice->status = 'void';
        $invoice->save();

        // If Stripe integration
        if ($invoice->stripe_invoice_id ?? false) {
            $stripe = new StripeClient(config('services.stripe.secret'));
            $stripe->invoices->voidInvoice($invoice->stripe_invoice_id);
        }
        $invoice->logActivity(
            'voided',
            'Invoice #' . $invoice->invoice_number . ' was marked as void by ' . (auth()->user()->name ?? 'System') . '.'
        );
        return response()->json(['message' => 'Invoice voided successfully.']);
    }

    public function reports(Request $request)
    {
        $this->authorize('reports', Invoice::class);
        $query = Invoice::with('customer');

        if ($request->filled('start_date')) {
            $query->whereDate('issue_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('issue_date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(10);

        $totalInvoices = Invoice::count();
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $unpaidInvoices = Invoice::where('status', 'unpaid')->count();
        $outstanding = Invoice::where('status', 'unpaid')->sum('amount');

        // Chart data (monthly totals)
        $chartLabels = Invoice::selectRaw('MONTHNAME(issue_date) as month')
            ->groupBy('month')->pluck('month');

        $chartData = Invoice::selectRaw('SUM(amount) as total')
            ->groupByRaw('MONTH(issue_date)')
            ->pluck('total');

        return view('invoices.reports', compact(
            'invoices', 'totalInvoices', 'paidInvoices', 'unpaidInvoices', 'outstanding',
            'chartLabels', 'chartData'
        ));
    }

}
