<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Helpers\FuzzySearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        try {
            $query = $request->input('q');
            if (!$query) {
                return response()->json([
                    'status' => 'success',
                    'results' => []
                ]);
            }

            $likePattern = '%' . $query . '%';

            // -------------------------
            // Customer Search
            // -------------------------
            $customers = Customer::where('name', 'LIKE', $likePattern)
                ->orWhere('email', 'LIKE', $likePattern)
                ->take(10)
                ->get()
                ->filter(function ($c) use ($query) {
                    return FuzzySearch::isSimilar($c->name, $query)
                        || FuzzySearch::isSimilar($c->email, $query)
                        || stripos($c->name, $query) !== false
                        || stripos($c->email, $query) !== false;
                })->values()
                ->map(function ($c) {
                    return [
                        'type'  => 'customer',
                        'label' => $c->name . ' (' . $c->email . ')',
                        'url'   => route('customers.show', $c->id) // use $id instead of getKey()
                    ];
                });

            // -------------------------
            // Invoice Search
            // -------------------------
            $invoices = Invoice::where('invoice_number', 'LIKE', $likePattern)
                ->orWhere('description', 'LIKE', $likePattern)
                ->take(10)
                ->get()
                ->filter(function ($i) use ($query) {
                    return FuzzySearch::isSimilar($i->invoice_number, $query)
                        || FuzzySearch::isSimilar($i->description, $query)
                        || stripos($i->invoice_number, $query) !== false
                        || stripos($i->description, $query) !== false;
                })->values()
                ->map(function ($i) {
                    return [
                        'type'  => 'invoice',
                        'label' => $i->invoice_number,
                        'url'   => route('invoices.show', $i->id)
                    ];
                });

            // -------------------------
            // Product Search
            // -------------------------
            $products = Product::where('name', 'LIKE', $likePattern)
                ->orWhere('category', 'LIKE', $likePattern)
                ->take(10)
                ->get()
                ->filter(function ($p) use ($query) {
                    return FuzzySearch::isSimilar($p->name, $query)
                        || FuzzySearch::isSimilar($p->category, $query)
                        || stripos($p->name, $query) !== false
                        || stripos($p->category, $query) !== false;
                })->values()
                ->map(function ($p) {
                    return [
                        'type'  => 'product',
                        'label' => $p->name,
                        'url'   => route('products.show', $p->id)
                    ];
                });

            // -------------------------
            // Merge all results
            // -------------------------
            // -------------------------

            $collections = collect([$customers, $invoices, $products])
                ->filter(fn($col) => $col && $col->isNotEmpty());

            $results = $collections->collapse()->values();

            // Debug
            // dd($customers, $invoices, $products, $results);

            return response()->json([
                'status' => 'success',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Custom Search Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Search failed. Please try again later.'
            ], 500);
        }
    }
}
