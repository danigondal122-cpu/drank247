<?php

namespace App\Mail;

use App\Models\Franchise;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class franchiseForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public Franchise $login_detail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Franchise $login_detail)
    {
        $this->login_detail = $login_detail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('franchise.mailtemplate.forgotpasswordmail');
    }
}
?>
