<?php

namespace App;

use App\Mail\NewUserAccountActivated;
use App\Mail\AdminNoticeNewUserCancelled;
use App\Mail\AdminNoticeNewUserPending;
use App\Mail\NewUserActivationCancelled;
use App\Mail\NewUserActivationPending;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

class ActivationService
{

    protected $mailer;

    protected $tokenRepo;

    protected $resendAfter = 24;

    protected $adminEmail;

    public function __construct(Mailer $mailer, TokenRepository $tokenRepo)
    {
        $this->mailer = $mailer;
        $this->tokenRepo = $tokenRepo;
        $this->adminEmail = config('auth.activation_admin_email');
    }

    public function sendActivationMail($user)
    {
        if ($user->activated || !$this->shouldSend($user)) {
            return;
        }

        $this->tokenRepo->createActivationTokens($user);

        Mail::to($this->adminEmail)
            ->send(new AdminNoticeNewUserPending($user));

        Mail::to($user->email)
            ->send(new NewUserActivationPending($user));
    }

    public function activateUser($token)
    {
        $token  = $this->tokenRepo->get($token, 'activate');

        if (is_null($token)) {
            return null;
        }

        $user = $token->user;

        $user->activated = true;

        $user->save();

        $this->tokenRepo->purgeTokensForUser($user);

        // Mail to user
        Mail::to($user->email)
            ->send(new NewUserAccountActivated($user));

        return $user;
    }

    public function cancelActivation($token)
    {
        $activation = $this->tokenRepo->get($token, 'cancel');

        if (is_null($activation)) {
            return null;
        }

        $user = User::find($activation->user_id);

        $this->tokenRepo->purgeTokensForUser($user);

        if ($user->activated) {
            return false;
        }

        Mail::to($this->adminEmail)
            ->send(new AdminNoticeNewUserCancelled($user));

        Mail::to($user->email)
            ->send(new NewUserActivationCancelled($user));

        $user->delete();

        return true;
    }

    private function shouldSend(User $user)
    {
        $token = $this->tokenRepo->getByUser($user, 'activate');
        return is_null($token) || strtotime($token->created_at) + 60 * 60 * $this->resendAfter < time();
    }

}
