<?php

namespace App\Http\Controllers;

use App\Models\InvoiceTemplate;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceTemplateController extends Controller
{
    public function index()
    {
        $orgId = Auth::user()->organization_id;
        $templates = InvoiceTemplate::availableFor($orgId)
            ->orderBy('sort_order')
            ->get();

        $settings = Setting::first();
        $activeTemplateId = $settings->default_template_id ?? null;
        $customCss = $settings->custom_invoice_css ?? '';

        return view('templates.index', compact('templates', 'activeTemplateId', 'customCss'));
    }

    public function preview(InvoiceTemplate $template)
    {
        $settings = Setting::first();
        $activeTemplateId = $settings->default_template_id ?? null;

        return view('templates.preview', compact('template', 'activeTemplateId'));
    }

    public function select(Request $request, InvoiceTemplate $template)
    {
        $settings = Setting::firstOrNew();
        $settings->default_template_id = $template->id;
        $settings->save();

        return back()->with('success', "Template '{$template->name}' selected successfully.");
    }

    public function customCss(Request $request)
    {
        $request->validate(['custom_css' => 'nullable|string|max:10000']);

        $settings = Setting::firstOrNew();
        $settings->custom_invoice_css = $request->input('custom_css', '');
        $settings->save();

        return back()->with('success', 'Custom CSS saved successfully.');
    }
}
