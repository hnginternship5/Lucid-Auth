<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class ZikiMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $secret;
    public $sourcekey;
    
    public function __construct(User $user, $secret, $sourcekey)
    {
        //
        $this->user = $user;
        $this->secret = $secret;
        $this->sourcekey = $sourcekey;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.Ziki');
    }
}
