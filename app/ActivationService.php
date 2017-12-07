<?php

namespace App;

use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class ActivationService
{

    protected $mailer;

    protected $activationRepo;

    protected $resendAfter = 24;

    protected $adminEmail;

    public function __construct(Mailer $mailer, ActivationRepository $activationRepo)
    {
        $this->mailer = $mailer;
        $this->activationRepo = $activationRepo;
        $this->adminEmail = config('auth.activation_admin_email');
    }

    public function sendActivationMail($user)
    {
        if ($user->activated || !$this->shouldSend($user)) {
            return;
        }

        // Mail to admin
        $token = $this->activationRepo->createActivation($user);
        $link = route('user.activate', $token);
        $message = "Hi,\n\nA new user just logged in:\n\n  Name: {$user->name}\n  E-mail: {$user->email}\n\nTo activate the account:\n{$link}\n\nBest,\nBlekkio";
        $this->mailer->raw($message, function (Message $message) {
            $message->to($this->adminEmail)
                ->subject('[Blekkio] New user waiting activation');
        });

        // Mail to user
        $message = "Hi {$user->name},\n\nWelcome to Blekkio! Your Blekkio account is awaiting manual activation. No action is needed on your behalf. You will receive a new e-mail once the account is activated.\n\nBest,\nBlekkio";
        $this->mailer->raw($message, function (Message $message) use ($user) {
            $message->to($user->email)
                ->subject('[Blekkio] Pending activation');
        });
    }

    public function activateUser($token)
    {
        $activation = $this->activationRepo->getActivationByToken($token);

        if ($activation === null) {
            return null;
        }

        $user = User::find($activation->user_id);

        $user->activated = true;

        $user->save();

        $this->activationRepo->deleteActivation($token);

        // Mail to user
        $message = "Hi {$user->name},\n\nWelcome to Blekkio! Your account has been activated, have fun!\n\nhttps://blekkio.uio.no/\n\nBest,\nBlekkio";
        $this->mailer->raw($message, function (Message $message) use ($user) {
            $message->to($user->email)
                ->subject('[Blekkio] Welcome!');
        });

        return $user;

    }

    private function shouldSend($user)
    {
        $activation = $this->activationRepo->getActivation($user);
        return $activation === null || strtotime($activation->created_at) + 60 * 60 * $this->resendAfter < time();
    }

}
