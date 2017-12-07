<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserActivationPending extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('[Blekkio] New account pending activation');

        $cancelLink = route('user.cancel', $this->user->getToken('cancel')->token);

        return $this->text('emails.new_user_activation_pending')
            ->with([
                'name' => $this->user->name,
                'cancelLink' => $cancelLink,
            ]);
    }
}
