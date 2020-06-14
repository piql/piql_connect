<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ConfirmUserRegistration extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $host;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $host)
    {
        $this->user = $user;
        $this->host = $host;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('users.emails.registration')
            ->with([
                'name' => $this->user->full_name,
                'confirmation_url' => $this->host.'/registration/confirm/'.$this->user->confirmation_token,
            ]);
    }
}
