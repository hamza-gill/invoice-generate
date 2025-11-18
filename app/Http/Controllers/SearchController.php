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

            $query = trim($request->input('q'));

            if (!$query) {
                return response()->json([
                    'status' => 'success',
                    'results' => []
                ]);
            }

            $likePattern = '%' . $query . '%';

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER SEARCH
            |--------------------------------------------------------------------------
            */
            $customers = Customer::where(function ($q) use ($likePattern) {
                $q->where('name', 'LIKE', $likePattern)
                    ->orWhere('email', 'LIKE', $likePattern);
            })
                ->take(10)
                ->get()
                ->filter(function ($c) use ($query) {
                    return FuzzySearch::isSimilar($c->name, $query)
                        || FuzzySearch::isSimilar($c->email, $query)
                        || stripos($c->name, $query) !== false
                        || stripos($c->email, $query) !== false;
                })
                ->values()
                ->map(function ($c) {
                    return [
                        'type'  => 'customer',
                        'label' => "{$c->name} ({$c->email})",
                        'url'   => route('customers.show', $c->id)
                    ];
                });


            /*
            |--------------------------------------------------------------------------
            | INVOICE SEARCH (Show invoice + customer info)
            |--------------------------------------------------------------------------
            */
            $invoices = Invoice::with('customer')
                ->where(function ($q) use ($likePattern) {
                    $q->where('invoice_number', 'LIKE', $likePattern)
                        ->orWhere('description', 'LIKE', $likePattern);
                })
                ->take(10)
                ->get()
                ->filter(function ($i) use ($query) {
                    return FuzzySearch::isSimilar($i->invoice_number, $query)
                        || FuzzySearch::isSimilar($i->description, $query)
                        || stripos($i->invoice_number, $query) !== false
                        || stripos($i->description, $query) !== false;
                })
                ->values()
                ->map(function ($i) {

                    $customerName = $i->customer?->name ?? 'Unknown Customer';

                    return [
                        'type'  => 'invoice',
                        'label' => "Invoice #{$i->invoice_number} — {$customerName} — {$i->total_amount} USD",
                        'url'   => route('invoices.show', $i->id)
                    ];
                });


            /*
            |--------------------------------------------------------------------------
            | PRODUCT SEARCH (Show name + category + price)
            |--------------------------------------------------------------------------
            */
            $products = Product::where(function ($q) use ($likePattern) {
                $q->where('name', 'LIKE', $likePattern)
                    ->orWhere('category', 'LIKE', $likePattern);
            })
                ->take(10)
                ->get()
                ->filter(function ($p) use ($query) {
                    return FuzzySearch::isSimilar($p->name, $query)
                        || FuzzySearch::isSimilar($p->category, $query)
                        || stripos($p->name, $query) !== false
                        || stripos($p->category, $query) !== false;
                })
                ->values()
                ->map(function ($p) {
                    return [
                        'type'  => 'product',
                        'label' => "{$p->name} — {$p->category} — {$p->price} USD",
                        'url'   => route('products.show', $p->id)
                    ];
                });


            /*
            |--------------------------------------------------------------------------
            | SAFE MERGE OF NON-EMPTY COLLECTIONS
            |--------------------------------------------------------------------------
            */
            $collections = collect([$customers, $invoices, $products])
                ->filter(fn($col) => $col && $col->isNotEmpty());

            $results = $collections->collapse()->values();


            return response()->json([
                'status'  => 'success',
                'results' => $results
            ]);

        } catch (\Exception $e) {

            Log::error('Search Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Search failed. Please try again later.'
            ], 500);
        }
    }

}
