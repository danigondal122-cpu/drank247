<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerCredentialsMail extends Mailable
{
	use Queueable, SerializesModels;

	public $data;

	public function __construct($data)
	{
		$this->data = $data;
		$this->data['title'] = 'New Registration';
	}

	public function envelope(): Envelope
	{
		return new Envelope(
			subject: 'Login Credential',
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'mail.customer-credentials',
		);
	}

	public function attachments(): array
	{
		return [];
	}
}
