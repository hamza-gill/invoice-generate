<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RecurringInvoice;
use Illuminate\Http\Request;
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

    public function store(Request $request)
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

        RecurringInvoice::create([
            'user_id' => Auth::id(),
            'customer_id' => $request->input('customer_id'),
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
        return view('recurring.show', compact('recurring'));
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
