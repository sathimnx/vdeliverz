<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendLoginCode extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $otp, $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->otp = $data['otp'];
        $this->name = $data['name'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('VDeliverz Verfication')->markdown('mail.otp');
    }
}
