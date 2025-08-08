<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentGrantApproved extends Mailable
{
    use Queueable, SerializesModels;

    protected $userGrantCode;
    protected $user;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $userGrantCode)
    {
        $this->userGrantCode = $userGrantCode;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Grant Application Approved!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emailtemplates.grant_approved_notify',
            with: [
                'userGrantCode' => $this->userGrantCode->code,
                'student_name' => $this->user->name,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
