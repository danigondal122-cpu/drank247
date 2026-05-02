<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
	use Queueable, SerializesModels;

	public $data;

	public function __construct($data)
	{
		$this->data = $data;
		$this->data['title'] = 'Contact Us';
	}

	public function envelope(): Envelope
	{
		return new Envelope(
			subject: 'Contact Us Mail',
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'mail.contact-us'
		);
	}

	public function attachments(): array
	{
		return [];
	}
}
