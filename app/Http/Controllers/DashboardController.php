<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // ✅ Stats
            $totalInvoices = Invoice::count();
            $totalPayments = Invoice::where('status', 'paid')->count();
            $unmatchedPayments = Invoice::where('status', 'unpaid')->count();

            // ✅ Monthly Revenue
            $monthlyRevenue = Invoice::where('status', 'paid')
                ->with('items')
                ->get()
                ->flatMap(fn($invoice) => $invoice->items)
                ->sum(fn($item) => $item->amount * $item->quantity);

            // ✅ Revenue Chart
            $revenueData = Invoice::select(
                DB::raw('MONTH(issue_date) as month_number'),
                DB::raw('MONTHNAME(issue_date) as month_name'),
                DB::raw('SUM(amount) as total')
            )
                ->groupBy('month_number', 'month_name')
                ->orderBy('month_number')
                ->get();

            $chartLabels = $revenueData->pluck('month_name');
            $chartValues = $revenueData->pluck('total');

            // ✅ Invoice Status Distribution
            $paidCount = Invoice::where('status', 'paid')->count();
            $pendingCount = Invoice::where('status', 'sent')->count();
            $overdueCount = Invoice::where('status', 'overdue')->count();

            // ✅ New Sections
            $recentInvoices = Invoice::with('customer')
                ->latest()
                ->take(5)
                ->get();

            $recentPaidInvoices = Invoice::with('customer')
                ->where('status', 'paid')
                ->latest()
                ->take(5)
                ->get();

            $recentCustomers = Customer::latest()->take(5)->get();

            return view('dashboard.index', compact(
                'totalInvoices',
                'totalPayments',
                'unmatchedPayments',
                'monthlyRevenue',
                'chartLabels',
                'chartValues',
                'paidCount',
                'pendingCount',
                'overdueCount',
                'recentInvoices',
                'recentPaidInvoices',
                'recentCustomers'
            ));
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', 'Failed to load dashboard data: ' . $e->getMessage());
        }
    }

}
