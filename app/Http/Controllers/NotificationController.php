<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\Invoice;

class NotificationController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $invoiceIds = Invoice::where('organization_id', $user->organization_id)->pluck('id');

            $notifications = DatabaseNotification::where('notifiable_type', Invoice::class)
                ->whereIn('notifiable_id', $invoiceIds)
                ->latest()
                ->paginate(10);

            return view('notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to load notifications: ' . $e->getMessage());
        }
    }

    public function markRead($id)
    {
        try {
            $user = auth()->user();
            $invoiceIds = Invoice::where('organization_id', $user->organization_id)->pluck('id');

            DatabaseNotification::where('id', $id)
                ->where('notifiable_type', Invoice::class)
                ->whereIn('notifiable_id', $invoiceIds)
                ->update(['read_at' => now()]);

            return back()->with('success', 'Notification marked as read.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark notification as read: ' . $e->getMessage());
        }
    }

    public function markAllRead()
    {
        try {
            $user = auth()->user();
            $invoiceIds = Invoice::where('organization_id', $user->organization_id)->pluck('id');

            DatabaseNotification::where('notifiable_type', Invoice::class)
                ->whereIn('notifiable_id', $invoiceIds)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return back()->with('success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to mark notifications as read: ' . $e->getMessage());
        }
    }
}
