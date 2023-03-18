<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterSuccess extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subject, $content, $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = $data['subject'];
        $this->content = $data['content'];
        $this->name = $data['name'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->markdown('mail.success');
    }
}