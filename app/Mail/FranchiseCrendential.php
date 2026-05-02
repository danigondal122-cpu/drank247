<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FranchiseCrendential extends Mailable
{
    use Queueable, SerializesModels;
    public $franchise_detail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($franchise_detail)
    {
      $this->franchise_detail = $franchise_detail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      // dd($this->franchise_detail);
        return $this->view('admin.mailtemplate.FranchiseLoginMail',$this->franchise_detail)->subject('Franchises Credential');
    }
}
