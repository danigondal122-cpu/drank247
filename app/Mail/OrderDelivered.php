<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderDelivered extends Mailable
{
    use Queueable, SerializesModels;
    public $maildata;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($maildata)
    {
      $this->maildata = $maildata;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
     
        $maildata=$this->maildata;
        return $this->view('api.mailtemplate.OrderDelivered',$maildata)->subject('Order Delivered')->attach(public_path('uploads/generatepdf/'.$maildata['file']), [
           
          ]);
    }
}
