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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @param ActivationService $activationService
     */
    public function __construct(ActivationService $activationService)
    {
        $this->middleware('guest', ['except' => ['samlLogout']]);
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

        if (!is_null($user->saml_session)) {
            return \Saml2::logout('/', $user->saml_id, $user->saml_session);
        }

        return $this->logout($request);
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
        abort(404);
    }
}
