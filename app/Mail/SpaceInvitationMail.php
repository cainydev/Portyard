<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SpaceInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invitation $invitation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been invited to join {$this->invitation->space->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.space-invitation',
            with: [
                'spaceName' => $this->invitation->space->name,
                'inviterName' => $this->invitation->inviter?->name ?? 'Someone',
                'role' => $this->invitation->role->value,
                'acceptUrl' => route('invitations.accept', $this->invitation->token),
                'declineUrl' => route('invitations.decline', $this->invitation->token),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
