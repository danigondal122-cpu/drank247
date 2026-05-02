<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
 
class CustomerLoginEmail extends Mailable
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
        return $this->view('admin.mailtemplate.CustomerLoginMail',$maildata)->subject('Login Email');
    }
}
?>