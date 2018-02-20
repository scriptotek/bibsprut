<?php

namespace App\Listeners;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Aacotroneo\Saml2\Events\Saml2LogoutEvent;
use App\ActivationService;
use App\TokenRepository;
use App\User;

class Saml2EventSubscriber
{

    public function __construct(ActivationService $activationService, TokenRepository $tokenRepo)
    {
        $this->activationService = $activationService;
        $this->tokenRepo = $tokenRepo;
    }

    /**
     * Handle the event.
     *
     * @param  Saml2LoginEvent $event
     * @return void
     */
    public function onUserLogin(Saml2LoginEvent $event)
    {
        $data = $event->getSaml2User();
        $uid = $data->getUserId();
        $attrs = $data->getAttributes();

        if (!$attrs['eduPersonPrincipalName'][0]) {
            \Log::notice('No eduPersonPrincipalName returned in SAML2 login event.');
            \Session::flash('error', 'An unknown error occured during login.');
            return;
        }

        $feideId = $attrs['eduPersonPrincipalName'][0];

        $user = User::firstOrNew(['feide_id' => $feideId]);

        if (!$user->exists) {
            $user->name = $attrs['cn'][0];
            // $user->email = $attrs['mail'][0];
            $user->email = "d.m.heggo@ub.uio.no";
            $user->save();

            $this->activationService->sendActivationMail($user);
            \Log::notice('Registered new SAML user.', ['feide' => $feideId]);
        }

        $user->saml_id = $uid;
        $user->saml_session = $data->getSessionIndex();
        $user->save();

        \Auth::login($user);
    }

    /**
     * Handle the event.
     *
     * @param  Saml2LogoutEvent  $event
     * @return void
     */
    public function onUserLogout(Saml2LogoutEvent $event)
    {
        $user = \Auth::user();
        if ($user) {
            $user->saml_id = null;
            $user->saml_session = null;
            $user->save();
        }
        \Auth::logout();
        \Session::save();
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events)
    {
        $events->listen(
            'Aacotroneo\Saml2\Events\Saml2LoginEvent',
            'App\Listeners\Saml2EventSubscriber@onUserLogin'
        );
        $events->listen(
            'Aacotroneo\Saml2\Events\Saml2LogoutEvent',
            'App\Listeners\Saml2EventSubscriber@onUserLogout'
        );
    }
}
