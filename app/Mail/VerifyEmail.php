<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verifikasi Email Anda — Mons Magna')
                    ->view('emails.verify-email')
                    ->with([
                        'name' => $this->user->NamaLengkap ?? $this->user->Username,
                        'token' => $this->token,
                    ]);
    }
}
