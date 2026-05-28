<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\UpdateIntegrationRequest;
use App\Http\Requests\Setting\UpdateInvoiceRequest;
use App\Http\Requests\Setting\UpdateOrganizationRequest;
use App\Http\Requests\Setting\UpdatePasswordRequest;
use App\Models\Setting;
use App\Models\WebhookSetting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('view', \App\Models\Setting::class);
        $setting = Setting::first();
        $webhookUrl = secure_url('/webhook');

        // Get webhook settings (or create a new instance if none exists)
        $webhookSetting = WebhookSetting::first() ?? new WebhookSetting();

        return view('settings.index', compact('setting', 'webhookSetting','webhookUrl'));
    }

    public function updateOrganization(UpdateOrganizationRequest  $request)
    {
        $this->authorize('updateOrganization', \App\Models\Setting::class);
        $validated = $request->validated();

        $setting = \App\Models\Setting::firstOrNew();

        // Handle logo upload
        // 🔹 Handle logo upload
        if ($request->hasFile('logo_path')) {
            // Delete old logo if exists
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            // Store new logo
            $path = $request->file('logo_path')->store('logos', 'public');
            $validated['logo_path'] = $path;
        }

        $setting->fill($validated)->save();

        return back()->with('success', 'Organization settings updated successfully.');
    }

    public function updateIntegration(UpdateIntegrationRequest  $request)
    {
        $this->authorize('updateIntegration', \App\Models\Setting::class);
        $validated = $request->validated();

        $setting = Setting::firstOrNew();
        $setting->fill($validated)->save();

        return back()->with('success', 'Integration settings updated successfully.');
    }

    public function updateInvoice(UpdateInvoiceRequest $request)
    {
        $this->authorize('updateInvoice', \App\Models\Setting::class);

        $validated = $request->validated();
        $setting = Setting::firstOrNew();

        try {
            // Basic invoice settings
            $setting->fill([
                'tax_id' => $validated['tax_id_invoice'] ?? null,
                'enable_tax_id' => $request->has('enable_tax_id'),
                'enable_terms' => $request->has('enable_terms'),
                'enable_invoice_notes' => $request->has('enable_invoice_notes'),
                'enable_tax' => $request->has('enable_tax'),
                'enable_due_date' => $request->has('enable_due_date'),
                'enable_rush_delivery' => $request->has('enable_rush_delivery'),
                'starting_invoice_number' => $validated['starting_invoice_number'],
            ]);

            // Handle rush delivery options
            if ($request->has('enable_rush_delivery') && $request->has('rush_options')) {
                $rushOptions = [];

                foreach ($request->rush_options as $option) {
                    $rushOptions[] = [
                        'label' => trim($option['label']),
                        'days' => $option['days'],
                        'fee' => floatval($option['fee']),
                    ];
                }

                $setting->rush_delivery_options = $rushOptions;
            } else {
                // If rush delivery is disabled, clear the options
                if (!$request->has('enable_rush_delivery')) {
                    $setting->rush_delivery_options = null;
                }
            }

            $setting->save();

            return back()->with('success', 'Invoice settings updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Failed to update invoice settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Failed to update invoice settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update invoice settings including rush delivery options
     */
    public function updateInvoiceSettings(Request $request)
    {
        $setting = Setting::firstOrFail();

        // Check authorization
        if (!Gate::allows('updateInvoice', $setting)) {
            return redirect()->back()->with('error', 'You do not have permission to update invoice settings.');
        }

        // Validate the request
        $validated = $request->validate([
            'tax_id_invoice' => 'nullable|string|max:255',
            'starting_invoice_number' => 'nullable|string|regex:/^INV-\d{4}-\d{3,}$/',
            'enable_terms' => 'nullable|boolean',
            'enable_invoice_notes' => 'nullable|boolean',
            'enable_due_date' => 'nullable|boolean',
            'enable_tax' => 'nullable|boolean',
            'enable_tax_id' => 'nullable|boolean',
            'enable_rush_delivery' => 'nullable|boolean',
            'rush_options' => 'nullable|array|min:1',
            'rush_options.*.label' => 'required_with:rush_options|string|max:255',
            'rush_options.*.days' => 'required_with:rush_options',
            'rush_options.*.fee' => 'required_with:rush_options|numeric|min:0',
        ]);

        try {
            // Update tax ID if provided
            if (isset($validated['tax_id_invoice'])) {
                $setting->tax_id = $validated['tax_id_invoice'];
            }

            // Update starting invoice number if provided
            if (isset($validated['starting_invoice_number'])) {
                $setting->starting_invoice_number = $validated['starting_invoice_number'];
            }

            // Update boolean fields (handle checkboxes - they won't be in request if unchecked)
            $setting->enable_terms = $request->has('enable_terms');
            $setting->enable_invoice_notes = $request->has('enable_invoice_notes');
            $setting->enable_due_date = $request->has('enable_due_date');
            $setting->enable_tax = $request->has('enable_tax');
            $setting->enable_tax_id = $request->has('enable_tax_id');
            $setting->enable_rush_delivery = $request->has('enable_rush_delivery');

            // Handle rush delivery options
            if ($request->has('enable_rush_delivery') && $request->has('rush_options')) {
                $rushOptions = [];

                foreach ($request->rush_options as $option) {
                    // Validate and format each option
                    $rushOptions[] = [
                        'label' => trim($option['label']),
                        'days' => $option['days'],
                        'fee' => floatval($option['fee']),
                    ];
                }

                $setting->rush_delivery_options = $rushOptions;

                Log::info('Rush delivery options saved', [
                    'options' => $rushOptions,
                    'count' => count($rushOptions)
                ]);
            } else {
                // If rush delivery is disabled, keep existing options or set to null
                if (!$request->has('enable_rush_delivery')) {
                    $setting->rush_delivery_options = null;
                }
            }

            // Save the settings
            $setting->save();

            return redirect()->back()->with('success', 'Invoice settings updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update invoice settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update invoice settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updatePassword(UpdatePasswordRequest  $request)
    {
        $this->authorize('updatePassword', \App\Models\Setting::class);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('success', 'Password updated successfully!');
    }

    public function togglePaymentGateway(Request $request)
    {
        $this->authorize('updateIntegration', \App\Models\Setting::class);

        $organization = Auth::user()->organization;
        $plan = $organization?->activeSubscription?->plan;

        if (! $plan?->payment_gateway_enabled) {
            return back()->with('error', 'Payment gateway is not included in your current plan. Please upgrade.');
        }

        $setting = Setting::firstOrNew();
        $setting->payment_gateway_enabled = $request->boolean('payment_gateway_enabled');
        $setting->save();

        return back()->with('success', 'Payment gateway setting updated.');
    }
}
