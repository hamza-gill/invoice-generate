<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::orderByRaw('FIELD(priority, "urgent", "high", "medium", "low")')
            ->orderByDesc('last_message_at')
            ->get();

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'message' => ['required', 'string', 'max:10000'],
        ]);

        $ticket = SupportTicket::create([
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
            'opened_by' => Auth::id(),
            'last_message_at' => now(),
            'is_read_by_admin' => false,
        ]);

        $ticket->messages()->create([
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'body' => $request->message,
        ]);

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Ticket created. Our team will reply shortly.');
    }

    public function show(SupportTicket $ticket)
    {
        // Ensure the customer owns this ticket (global org scope applies).
        $ticket->load('opener', 'messages');

        // Mark the thread as read by the organization.
        $ticket->update(['is_read_by_org' => true]);
        SupportMessage::where('support_ticket_id', $ticket->id)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $ticket->messages;

        return view('support.show', compact('ticket', 'messages'));
    }

    public function sendMessage(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:10000'],
        ]);

        $message = $ticket->messages()->create([
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'body' => $request->message,
        ]);

        $ticket->update([
            'last_message_at' => now(),
            'is_read_by_admin' => false,
            'is_read_by_org' => true,
        ]);

        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'time' => $message->created_at->format('M d, g:i A'),
            'sender' => Auth::user()->first_name ?: Auth::user()->name,
            'is_self' => true,
        ]);
    }

    public function poll(Request $request, SupportTicket $ticket)
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = SupportMessage::where('support_ticket_id', $ticket->id)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'time' => $m->created_at->format('M d, g:i A'),
                'sender' => $m->sender_type === 'admin' ? 'Inveqi Support' : ($m->sender()->first_name ?: $m->sender()->name),
                'is_self' => $m->sender_type === 'user',
                'is_admin' => $m->sender_type === 'admin',
            ]);

        // Clear the incoming (admin) unread indicator when the customer is polling.
        if ($messages->isNotEmpty()) {
            $ticket->update(['is_read_by_org' => true]);
            SupportMessage::where('support_ticket_id', $ticket->id)
                ->where('sender_type', 'admin')
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return response()->json(['messages' => $messages]);
    }
}
