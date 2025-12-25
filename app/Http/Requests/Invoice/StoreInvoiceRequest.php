<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Optionally restrict to logged-in users
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // 🧍‍♂️ Customer Fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'customer_id' => 'nullable|integer|exists:customers,id',

            // 🧾 Invoice Details
            'invoice_number' => 'nullable|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'project_address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',

            // 🧮 Line Items
            'line_items' => 'required|array|min:1',
            'line_items.*.description' => 'required|string|max:255',
            'line_items.*.product_id' => 'required|integer|exists:products,id',
            'line_items.*.quantity' => 'required|numeric|min:1',
            'line_items.*.unit_price' => 'required|numeric|min:0',

            // ⚡ Rush Add-On
            'enable_rush_addon' => 'nullable|boolean',
            'rush_delivery_type' => 'nullable|string|in:shipping,electronic',
            'rush_description' => 'nullable|string|max:500',
            'rush_fee' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Custom attribute names for clearer error messages.
     */
    public function attributes(): array
    {
        return [
            'line_items.*.description' => 'line item description',
            'line_items.*.product_id' => 'line item product',
            'line_items.*.quantity' => 'line item quantity',
            'line_items.*.unit_price' => 'line item unit price',
        ];
    }
}
