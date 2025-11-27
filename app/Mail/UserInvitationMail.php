<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $url = route('invitation.accept', $this->user->invitation_token);
        $companyName = config('settings.company_name')
            ?? config('app.name')
            ?? 'SUSAN W CASE INC';
        return $this->subject("You are invited to join {$companyName}")
            ->view('emails.user_invitation', compact('url'));
    }
}
