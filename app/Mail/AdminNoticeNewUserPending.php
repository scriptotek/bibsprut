<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminNoticeNewUserPending extends Mailable
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

        $activateLink = route('user.activate', $this->user->getToken('activate')->token);
        $cancelLink = route('user.cancel', $this->user->getToken('cancel')->token);

        return $this->text('emails.admin_notice_new_user_pending')
            ->with([
                'name' => $this->user->name,
                'email' => $this->user->email,
                'activateLink' => $activateLink,
                'cancelLink' => $cancelLink,
            ]);
    }
}
