<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class SendPinMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $info;
    protected $type;
    public function __construct($pin,$type = 0)
    {
        $this->info = $pin;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {   
        if($this->type == 0){
            $subject = 'Your Pin For Register In '.env('APP_NAME');
            return $this->view('email_pin')->with('info',$this->info)->with('type',$this->type)->subject($subject);
        } else {
            $subject = 'Invitaion for Register In '.env('APP_NAME');
            return $this->view('email_pin')->with('info',$this->info)->with('type',$this->type)->subject($subject);
        }
    }
}