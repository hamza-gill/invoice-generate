<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function fetch(Request $request)
    {
        $search = $request->get('q', '');

        try {
            $customers = \App\Models\Customer::query()
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->limit(10)
                ->get();

            // If no customers found, you can handle it gracefully
            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No customers found.'
                ]);
            }

            $results = $customers->map(function ($c) {
                return [
                    'id' => $c->id,
                    'text' => "{$c->first_name } {$c->last_name} ({$c->email})",
                    'name' => $c->first_name . ' '. $c->last_name,
                    'first_name' => $c->first_name ,
                    'last_name' =>  $c->last_name,
                    'company_name' => $c->company_name,
                    'email' => $c->email,
                    'address' => $c->address,
                    'city' => $c->city,
                    'country' => $c->country,
                    'state' => $c->state,
                    'postal_code' => $c->postal_code,
                ];
            });

            return response()->json(['success' => true, 'data' => $results]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}

