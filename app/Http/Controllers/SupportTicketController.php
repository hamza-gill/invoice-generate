<?php

namespace App\Http\Controllers;

use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,zip,ppt,pptx', 'max:10240'],
        ]);

        $ticket = SupportTicket::create([
            'subject' => $request->subject,
            'priority' => $request->priority,
            'status' => 'open',
            'opened_by' => Auth::id(),
            'last_message_at' => now(),
            'is_read_by_admin' => false,
        ]);

        $message = $ticket->messages()->create([
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'body' => $request->message,
        ]);

        $this->storeAttachments($request->file('attachments', []), $ticket, $message);

        return redirect()->route('support.show', $ticket)
            ->with('success', 'Ticket created. Our team will reply shortly.');
    }

    /**
     * Persist uploaded files onto a message, returning their metadata.
     */
    protected function storeAttachments(array $files, SupportTicket $ticket, SupportMessage $message): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $filename = Str::random(32) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs(
                "tickets/{$ticket->id}/messages/{$message->id}",
                $filename,
                'support'
            );

            $attachment = $message->attachments()->create([
                'support_ticket_id' => $ticket->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            $attachments[] = [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'is_image' => $attachment->isImage(),
                'human_size' => $attachment->humanSize(),
                'download_url' => route('support.attachments.download', $attachment),
            ];
        }

        return $attachments;
    }


    public function show(SupportTicket $ticket)
    {
        // Ensure the customer owns this ticket (global org scope applies).
        $ticket->load('opener', 'messages.attachments');

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
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx,xls,xlsx,csv,txt,zip,ppt,pptx', 'max:10240'],
        ]);

        $message = $ticket->messages()->create([
            'sender_type' => 'user',
            'sender_id' => Auth::id(),
            'body' => $request->message,
        ]);

        $attachments = $this->storeAttachments($request->file('attachments', []), $ticket, $message);

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
            'attachments' => $attachments,
        ]);
    }

    /**
     * Download an attachment if the authenticated customer can access its ticket.
     */
    public function download(Request $request, SupportAttachment $attachment)
    {
        // Re-resolve under the org scope: returns null if the ticket belongs to
        // another organization, denying cross-tenant access.
        $ticket = SupportTicket::find($attachment->support_ticket_id);

        if (! $ticket) {
            abort(404);
        }

        if (! Storage::disk('support')->exists($attachment->stored_path)) {
            abort(404);
        }

        return Storage::disk('support')->download(
            $attachment->stored_path,
            $attachment->original_name
        );
    }

    public function poll(Request $request, SupportTicket $ticket)
    {
        $afterId = (int) $request->query('after_id', 0);

        $messages = SupportMessage::with('attachments')
            ->where('support_ticket_id', $ticket->id)
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
                'attachments' => $m->attachments->map(fn ($a) => [
                    'id' => $a->id,
                    'original_name' => $a->original_name,
                    'is_image' => $a->isImage(),
                    'human_size' => $a->humanSize(),
                    'download_url' => route('support.attachments.download', $a),
                ])->values(),
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
