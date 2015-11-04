<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class GoogleOauth
{
    protected $client;

    /**
     * Create a new middleware instance.
     *
     * @param Application|\Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->client = $app->make('google.api.client');
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // https://developers.google.com/identity/protocols/OAuth2WebServer
        $this->client->setAccessType('offline');

        // To get a refresh token, per <http://stackoverflow.com/a/31237203/489916>
        $this->client->setApprovalPrompt('force');

        if (\Storage::disk('local')->exists('google_access_token.json')) {
            $token = \Storage::disk('local')->get('google_access_token.json');
            $this->client->setAccessToken($token);
        }

        if ($request->path() == 'oauth2callback' && $request->has('code')) {

            if ($request->session()->get('google_oauth_state') !== $request->get('state')) {
                abort(500, 'The session state did not match.');
            }

            $this->client->authenticate($request->get('code'));

            \Storage::disk('local')->put('google_access_token.json', $this->client->getAccessToken());

            return redirect('/');

        } elseif (is_null($this->client->getAccessToken())) {

            // If the user hasn't authorized the app, initiate the OAuth flow
            $state = strval(mt_rand());
            $this->client->setState($state);
            $request->session()->put('google_oauth_state', $state);
            $authUrl = $this->client->createAuthUrl();

            return response()->view('google.authorize', ['authUrl' => $authUrl]);

        }
        return $next($request);
    }
}
