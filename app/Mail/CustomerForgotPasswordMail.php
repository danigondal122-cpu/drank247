<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerForgotPasswordMail extends Mailable
{
	use Queueable, SerializesModels;

	public $data;

	public function __construct($data)
	{
		$this->data = $data;
		$this->data['title'] = 'Password Reset';
		$this->data['reset_url'] = route('customer.reset.password', [
			'id' => $data['id'],
			'token' => $data['customer_reset_token'],
		]);
	}

	public function envelope(): Envelope
	{
		return new Envelope(
			subject: 'Forgot Password',
		);
	}

	public function content(): Content
	{
		return new Content(
			view: 'mail.customer-forgot-password',
		);
	}

	public function attachments(): array
	{
		return [];
	}
}
