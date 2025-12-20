<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of all products.
     */
    public function index()
    {
        $this->authorize('viewAny', Product::class);
        try {
            $products = Product::orderBy('created_at', 'desc')->paginate(10);
            return view('products.index', compact('products'));
        } catch (\Throwable $e) {
            Log::error('Error loading products: ' . $e->getMessage());
            return back()->with('error', 'Unable to load products. Please try again.');
        }
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $this->authorize('create', Product::class);
        return view('products.create');
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Product::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        try {
            Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'category' => $request->category,
                'description' => $request->description,
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Throwable $e) {

            Log::error('Error creating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified product with related invoices.
     */
    public function show(Product $product)
    {
        $this->authorize('viewAny', Product::class);
        $invoices = \App\Models\Invoice::whereHas('items', function ($query) use ($product) {
            $query->where('product_id', $product->id);
        })->with('customer')->get();

        $totalInvoices = $invoices->count();
        $paidInvoices = $invoices->where('status', 'paid')->count();
        $pendingInvoices = $invoices->where('status', '!=', 'paid')->count();

        return view('products.show', compact(
            'product',
            'invoices',
            'totalInvoices',
            'paidInvoices',
            'pendingInvoices'
        ));
    }


    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $product->update([
                'name' => $request->name,
                'price' => $request->price,
                'category' => $request->category,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Error updating product: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove (soft delete or hard delete) the specified product.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        try {
            $product->delete();
            return back()->with('success', 'Product deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete product. Please try again.');
        }
    }

    /**
     * Toggle product active/inactive (via AJAX or route).
     */
    public function toggleStatus(Product $product)
    {
        $this->authorize('toggleStatus', $product);
        try {
            $product->is_active = !$product->is_active;
            $product->save();

            return response()->json([
                'success' => true,
                'status' => $product->is_active ? 'active' : 'disabled'
            ]);
        } catch (\Throwable $e) {
            Log::error('Error toggling product status: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Unable to change status.'], 500);
        }
    }
}
