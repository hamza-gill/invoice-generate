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
                $q->where('first_name', 'LIKE', $likePattern)
                    ->orWhere('last_name', 'LIKE', $likePattern)
                    ->orWhere('email', 'LIKE', $likePattern)
                    ->orWhere('company_name', 'LIKE', $likePattern);
            })
                ->take(10)
                ->get()
                ->filter(function ($c) use ($query) {
                    $fullName = trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? ''));
                    return ($fullName && (FuzzySearch::isSimilar($fullName, $query) || stripos($fullName, $query) !== false))
                        || ($c->email && (FuzzySearch::isSimilar($c->email, $query) || stripos($c->email, $query) !== false))
                        || ($c->company_name && stripos($c->company_name, $query) !== false);
                })
                ->values()
                ->map(function ($c) {
                    $fullName = trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? ''));
                    return [
                        'type'  => 'customer',
                        'label' => "{$fullName} (" . ($c->email ?? 'No email') . ")",
                        'url'   => route('customers.show', $c->id)
                    ];
                });


            /*
            |--------------------------------------------------------------------------
            | INVOICE SEARCH (Show invoice + customer info)
            | Search by: invoice_number, amount, project_address, description
            | Also search by related customer name and email
            |--------------------------------------------------------------------------
            */
            $invoices = Invoice::with('customer')
                ->where('user_id', auth()->id())
                ->where(function ($q) use ($likePattern, $query) {
                    $q->where('invoice_number', 'LIKE', $likePattern)
                        ->orWhere('description', 'LIKE', $likePattern)
                        ->orWhere('project_address', 'LIKE', $likePattern)
                        ->orWhere('amount', 'LIKE', $likePattern);

                    // Search by customer name or email if invoice has customer relationship
                    $q->orWhereHas('customer', function ($customerQuery) use ($likePattern) {
                        $customerQuery->where('first_name', 'LIKE', $likePattern)
                            ->orWhere('last_name', 'LIKE', $likePattern)
                            ->orWhere('email', 'LIKE', $likePattern);
                    });
                })
                ->take(10)
                ->get()
                ->filter(function ($i) use ($query) {
                    $invoiceNum = $i->invoice_number ?? '';
                    $desc = $i->description ?? '';
                    $address = $i->project_address ?? '';
                    $amount = (string) ($i->amount ?? '');

                    $matchesInvoice =
                        ($invoiceNum && (FuzzySearch::isSimilar($invoiceNum, $query) || stripos($invoiceNum, $query) !== false))
                        || ($desc && (FuzzySearch::isSimilar($desc, $query) || stripos($desc, $query) !== false))
                        || ($address && (FuzzySearch::isSimilar($address, $query) || stripos($address, $query) !== false))
                        || ($amount && stripos($amount, $query) !== false);

                    $matchesCustomer = false;
                    if ($i->customer) {
                        $custName = $i->customer->first_name
                            ? trim(($i->customer->first_name ?? '') . ' ' . ($i->customer->last_name ?? ''))
                            : ($i->customer->name ?? '');
                        $matchesCustomer =
                            ($custName && (FuzzySearch::isSimilar($custName, $query) || stripos($custName, $query) !== false))
                            || ($i->customer->email && (FuzzySearch::isSimilar($i->customer->email, $query) || stripos($i->customer->email, $query) !== false));
                    }

                    return $matchesInvoice || $matchesCustomer;
                })
                ->values()
                ->map(function ($i) {

                    $customerName = $i->customer
                        ? trim(($i->customer->first_name ?? '') . ' ' . ($i->customer->last_name ?? '')) ?: 'Unknown Customer'
                        : 'Unknown Customer';

                    return [
                        'type'  => 'invoice',
                        'label' => "Invoice #{$i->invoice_number} — {$customerName} — {$i->amount} USD",
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
                    $cat = $p->category ?? '';
                    return ($p->name && (FuzzySearch::isSimilar($p->name, $query) || stripos($p->name, $query) !== false))
                        || ($cat && (FuzzySearch::isSimilar($cat, $query) || stripos($cat, $query) !== false));
                })
                ->values()
                ->map(function ($p) {
                    return [
                        'type'  => 'product',
                        'label' => "{$p->name} — " . ($p->category ?? 'Uncategorized') . " — {$p->price} USD",
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
                'message' => 'Search failed. Please try again later.',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

}
