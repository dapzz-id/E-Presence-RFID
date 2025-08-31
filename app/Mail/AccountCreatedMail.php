<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $logoCid;

    /**
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Akun Siswa Berhasil Dibuat')
            ->view('emails.account-created')
            ->with([
                'name'     => $this->data['name'],
                'username' => $this->data['username'],
                'email'    => $this->data['email'],
            ]);
    }
}