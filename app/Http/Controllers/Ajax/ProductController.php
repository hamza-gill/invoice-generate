<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportCustomersRequest;
use App\Models\Customer;
use App\Models\Product;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use AuthorizesRequests;
    /**
     * Fetch products dynamically (AJAX) with optional search.
     *
     * Supports:
     * - Live search for product name or SKU.
     * - Optional pagination limit.
     * - Returns JSON formatted for dropdowns or autocomplete.
     */
    public function fetch(Request $request)
    {
        $this->authorize('fetch', Product::class);
        try {
            $query = Product::query()->where('is_active', true);

            // Apply search filter (name or SKU)
            if ($request->filled('q')) {
                $search = $request->get('q');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // Optional: limit results for dropdown/autocomplete
            $limit = $request->get('limit', 10);
            $products = $query->orderBy('name')->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'stock' => $product->stock,
                        'label' => "{$product->name} - $ {$product->price}",
                    ];
                })
            ]);
        } catch (\Throwable $e) {
            Log::error('Error fetching products (AJAX): ' . $e->getMessage());

            return response()->json(['success' => false, 'error' => 'Unable to fetch products.'], 500);
        }
    }

    /**
     * AJAX search endpoint for DataTables or global product list search.
     * Returns paginated JSON response.
     */
    public function search(Request $request)
    {
        $this->authorize('fetch', Product::class);
        try {
            $search = $request->get('search');
            $query = Product::query();

            if (!empty($search)) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            }

            $products = $query->orderBy('created_at', 'desc')->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ]
            ]);
        } catch (\Throwable $e) {
            Log::error('Error during product search: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Search failed.'], 500);
        }
    }


     public function import(ImportCustomersRequest $request)
    {
        $this->authorize('import', Product::class);
        try {
            // Ensure file exists and is readable
            if (!$request->hasFile('file')) {
                throw new Exception('No file uploaded.');
            }

            $path = $request->file('file')->getRealPath();

            if (!file_exists($path)) {
                throw new Exception('Uploaded file not found on server.');
            }

            $file = fopen($path, 'r');
            if (!$file) {
                throw new Exception('Unable to open the uploaded file.');
            }

            $header = fgetcsv($file);

            if (!$header || count($header) === 0) {
                throw new Exception('The CSV file appears to be empty or missing headers.');
            }

            // Normalize header keys (trim, lowercase, replace spaces with underscores)
            $header = array_map(fn($h) => Str::slug(trim($h), '_'), $header);

            $count = 0;
            $skipped = 0;

            while (($row = fgetcsv($file)) !== false) {
                $data = @array_combine($header, $row);

                if (!$data) {
                    $skipped++;
                    continue;
                }

                if (empty($data['productservice_name'])) {
                    $skipped++;
                    continue; // Skip invalid or empty email rows
                }

                Product::updateOrCreate(
                    ['name' => trim($data['productservice_name'])],
                    [
                        'description' => $data['sales_description']  ?? null,
                        'price' => $data['price'] ?? null,
                        'category' => $data['category'] ?? null,
                    ]
                );

                $count++;
            }

            fclose($file);

            $message = "{$count} products imported successfully.";
            if ($skipped > 0) {
                $message .= " {$skipped} rows were skipped due to missing or invalid data.";
            }

            return redirect()
                ->route('products.index')
                ->with('success', $message);
        } catch (Exception $e) {
            // Log detailed error for developers
            Log::error('products import failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Return user-friendly error message
            return redirect()
                ->back()
                ->with('error', 'There was an error importing the products: ' . $e->getMessage());
        }
    }
}
