<?php

namespace App\Http\Controllers;

use App\Http\Requests\Setting\UpdateIntegrationRequest;
use App\Http\Requests\Setting\UpdateInvoiceRequest;
use App\Http\Requests\Setting\UpdateOrganizationRequest;
use App\Http\Requests\Setting\UpdatePasswordRequest;
use App\Models\Setting;
use App\Models\WebhookSetting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
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

        // Get webhook settings (or create a new instance if none exists)
        $webhookSetting = WebhookSetting::first() ?? new WebhookSetting();

        return view('settings.index', compact('setting', 'webhookSetting'));
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

    public function updateInvoice(UpdateInvoiceRequest  $request)
    {
        $this->authorize('updateInvoice', \App\Models\Setting::class);

        $validated = $request->validated();
        $setting = Setting::firstOrNew();

        $setting->fill([
            'tax_id' => $validated['tax_id_invoice'] ?? null,
            'enable_tax_id' => $request->has('enable_tax_id'),
            'enable_terms' => $request->has('enable_terms'),
            'enable_invoice_notes' => $request->has('enable_invoice_notes'),
            'enable_tax' => $request->has('enable_tax'),
            'enable_due_date' => $request->has('enable_due_date'),
            'starting_invoice_number' => $validated['starting_invoice_number'],
        ])->save();

        return back()->with('success', 'Invoice settings updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest  $request)
    {
        $this->authorize('updatePassword', \App\Models\Setting::class);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();
        return back()->with('success', 'Password updated successfully!');
    }
}
