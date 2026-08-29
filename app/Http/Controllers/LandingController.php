<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\RecurringInvoice;
use App\Models\SubscriptionPlan;

class LandingController extends Controller
{
    protected function siteStats()
    {
        $year = date('Y');

        return [
            ['value' => number_format(Organization::count()), 'label' => 'Active businesses'],
            ['value' => '$' . number_format(Invoice::whereYear('issue_date', $year)->sum('amount')), 'label' => 'Invoiced this year'],
            ['value' => number_format(Invoice::count()), 'label' => 'Invoices processed'],
            ['value' => number_format(RecurringInvoice::count()), 'label' => 'Recurring invoices running'],
        ];
    }

    protected function testimonials()
    {
        return Customer::query()
            ->with('organization')
            ->get()
            ->map(fn ($c) => [
                'name' => $c->full_name ?: 'A valued customer',
                'company' => $c->company_name ?: ($c->organization->name ?? 'Inveqi customer'),
                'initials' => collect(explode(' ', $c->full_name ?: 'Inveqi'))
                    ->map(fn ($n) => strtoupper($n[0]))
                    ->join(''),
            ])
            ->values()
            ->all();
    }

    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $stats = $this->siteStats();
        $testimonials = $this->testimonials();
        $companies = Organization::limit(6)->pluck('name')->all();
        $businessesCount = Organization::count();

        return view('landing.index', compact('plans', 'stats', 'testimonials', 'companies', 'businessesCount'));
    }

    public function pricing()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('landing.pricing', compact('plans'));
    }

    public function seo($slug)
    {
        $page = config('seo_pages.pages.' . $slug);

        abort_unless($page, 404);

        $common = config('seo_pages.common', []);

        $page = array_merge($common, $page);

        $page['schema'] = str_replace(
            '__CANONICAL__',
            url('/' . $slug),
            $page['schema'] ?? ''
        );

        $testimonials = $this->testimonials();
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        $businessesCount = Organization::count();

        return view('landing.seo', compact('page', 'testimonials', 'plans', 'businessesCount'));
    }
}
