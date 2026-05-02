<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlaceOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

	public function __construct(Order $order)
	{
		$this->data = [
            'title' => 'Order Confirmation',
            'customer' => $order->customer()->first(),
            'address' => $order->address()->first(),
            'order' => $order,
            // 'qrcode' => QrCode::size(100)->generate($order->id),
            'cart' => $order->orderDetails()->get(),
        ];
	}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.place-order',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
