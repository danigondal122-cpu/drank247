<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DelieveryPersonLogin extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name:string,email:string,password:string,}  $maildata
     */
    public $maildata;

    /**
     * Create a new message instance.
     *
     * @param  array{name:string,email:string,password:string,}  $maildata
     * @return void
     */
    public function __construct($maildata)
    {

        $this->maildata = $maildata;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login Credential',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.delivery-person-login',
            with: $this->maildata,
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
