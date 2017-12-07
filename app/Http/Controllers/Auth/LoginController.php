<?php

namespace App\Http\Controllers\Auth;

use App\ActivationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    protected $activationService;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/videos';

    /**
     * Create a new controller instance.
     *
     * @param ActivationService $activationService
     */
    public function __construct(ActivationService $activationService)
    {
        $this->middleware('guest', ['except' => ['samlLogout', 'activateUser', 'cancelActivation']]);
        $this->activationService = $activationService;
    }

    /**
     * Display SAML errors.
     *
     * @return \Illuminate\Http\Response
     */
    public function error()
    {
        return view('auth.error', [
            'errors' => session()->get('saml2_error', []),
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function samlLogout(Request $request)
    {
        $user = \Auth::user();

        return \Saml2::logout('/videos', $user->saml_id, $user->saml_session);
    }

    public function authenticated(Request $request, $user)
    {
        if (!$user->activated) {
            $this->activationService->sendActivationMail($user);
            auth()->logout();
            return back()->with('error', 'You need to confirm your account. We have sent you an activation code, please check your email.');
        }
        return redirect()->intended($this->redirectPath());
    }

    public function activateUser($token)
    {
        if ($user = $this->activationService->activateUser($token)) {
            \Session::flash('status', 'The account was activated!');
            return redirect($this->redirectPath());
        }

        \Session::flash('error', 'User already activated or not found!');
        return redirect($this->redirectPath());
    }

    public function cancelActivation($token)
    {
        if ($user = $this->activationService->cancelActivation($token)) {
            \Session::flash('status', 'Your account has been removed!');
            return redirect($this->redirectPath());
        }

        \Session::flash('error', 'User already activated or not found!');
        return redirect($this->redirectPath());
    }
}
