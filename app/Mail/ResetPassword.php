<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;


class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $userName = "";
    public $token = '';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username,$token)
    {
        $this->userName = $username;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('ahmedbensaber@gmail.com')
        ->subject('Reset Passsword')
        ->markdown('email.markdown-resetPassword');

    }
}
