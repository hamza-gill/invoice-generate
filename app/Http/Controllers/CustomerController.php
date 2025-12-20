<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportCustomersRequest;
use App\Models\Customer;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Customer::class);
        // This ensures $customers is a LengthAwarePaginator
        $customers = Customer::with('invoices')->latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }



    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);
        $customer->load('invoices');
        return view('customers.show', compact('customer'));
    }

    public function create()
    {
        $this->authorize('create', Customer::class);
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Customer::class);
        // Validate incoming request
        $validatedData = $request->validate([
            'name'              => 'required|string|max:255',
            'company_name'      => 'nullable|string|max:255',
            'email'             => 'required|email|unique:customers,email',
            'phone_number'      => 'nullable|string|max:50',
            'address'           => 'nullable|string|max:255',
            'postal_code'       => 'nullable|string|max:20',
            'city'              => 'nullable|string|max:100',
            'state'             => 'nullable|string|max:100',
            'country'           => 'nullable|string|max:100',
        ]);

        try {
            // Optionally generate a Stripe customer ID (dummy example)
            $validatedData['stripe_customer_id'] = 'cus_' . Str::random(12);

            // Create the customer
            Customer::create($validatedData);

            return redirect()
                ->route('customers.index')
                ->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Customer creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'data'  => $validatedData,
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create customer. Please try again or contact support.');
        }
    }

    public function import(ImportCustomersRequest $request)
    {
        $this->authorize('import', Customer::class);

        try {
            // Validate file exists
            if (!$request->hasFile('file')) {
                throw new Exception('No file uploaded.');
            }

            $file = $request->file('file');
            $path = $file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());

            if (!file_exists($path)) {
                throw new Exception('Uploaded file not found on server.');
            }

            $rows = [];

            // Read CSV or TXT
            if (in_array($extension, ['csv', 'txt'])) {
                $handle = fopen($path, 'r');
                if (!$handle) {
                    throw new Exception('Unable to open the uploaded file.');
                }

                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }

                fclose($handle);

                // Read Excel files
            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                $excelData = Excel::toArray([], $file);
                $rows = $excelData[0] ?? []; // first sheet
            } else {
                throw new Exception('Unsupported file type.');
            }

            if (empty($rows)) {
                throw new Exception('The file appears to be empty.');
            }

            // Normalize headers
            $header = array_map(fn($h) => Str::slug(trim($h), '_'), array_shift($rows));

            $count = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                // Normalize row length to match header
                $row = array_slice($row, 0, count($header));
                $row = array_pad($row, count($header), '');

                $data = array_combine($header, $row);

                // Skip rows with empty email
                if (empty($data['email'])) {
                    $skipped++;
                    continue;
                }

                Customer::updateOrCreate(
                    ['email' => trim($data['email'])],
                    [
                        'name' => $data['name'] ?? null,
                        'company_name' => $data['company_name'] ?? $data['company'] ?? null,
                        'address' => $data['street_address'] ?? $data['street'] ?? null,
                        'city' => $data['city'] ?? null,
                        'state' => $data['state'] ?? null,
                        'country' => $data['country'] ?? null,
                        'postal_code' => $data['zip'] ?? $data['postal_code'] ?? null,
                        'phone_number' => $data['phone'] ?? null,
                    ]
                );

                $count++;
            }

            $message = "{$count} customers imported successfully.";
            if ($skipped > 0) {
                $message .= " {$skipped} rows were skipped due to missing or invalid email.";
            }

            return redirect()
                ->route('customers.index')
                ->with('success', $message);

        } catch (Exception $e) {
            Log::error('Customer import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'There was an error importing the customers: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $this->authorize('search', Customer::class);
        try {
            $query = $request->input('query');

            $customers = \App\Models\Customer::withCount('invoices')
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($sub) use ($query) {
                        $sub->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%")
                            ->orWhere('company_name', 'like', "%{$query}%")
                            ->orWhere('country', 'like', "%{$query}%")
                            ->orWhere('city', 'like', "%{$query}%")
                            ->orWhere('state', 'like', "%{$query}%")
                            ->orWhere('postal_code', 'like', "%{$query}%")
                            ->orWhere('address', 'like', "%{$query}%");
                    });
                })
                ->orderBy('name', 'asc')
                ->limit(30)
                ->get();

            return response()->json([
                'success' => true,
                'message' => $customers->isEmpty()
                    ? 'No matching customers found.'
                    : 'Customers retrieved successfully.',
                'count' => $customers->count(),
                'data' => $customers
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while searching for customers.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


}
