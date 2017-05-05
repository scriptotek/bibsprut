<?php

namespace App\Http\Controllers;

use App\GoogleAccount;
use Illuminate\Http\Request;
use PulkitJalan\Google\Client as GoogleClient;

class GoogleAuthController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function logout(Request $request)
    {
        $email = $request->input('email');
        $account = GoogleAccount::where([
            'id' => $email,
        ])->first();

        if (!is_null($account)) {
            $account->delete();
        }

        $request->session()->flash('status', 'Logget ut ' . $account->userinfo['name'] );

        return redirect()->back();
    }

    protected function initiate(Request $request, GoogleClient $client)
    {
        $state = strval(mt_rand());
        $client->setState($state);
        $request->session()->put('google_oauth_state', $state);

        // Per https://developers.google.com/identity/protocols/OAuth2WebServer
        $client->setAccessType('offline');

        // To always get a refresh token, per <http://stackoverflow.com/a/31237203/489916>
        $client->setApprovalPrompt('force');

        $authUrl = $client->createAuthUrl();

        return redirect()->to($authUrl);
    }

    protected function processCallback(Request $request, GoogleClient $clientFactory)
    {
        $client = $clientFactory->getClient();

        // Per https://developers.google.com/identity/protocols/OAuth2WebServer
        $client->setAccessType('offline');

        // To always get a refresh token, per <http://stackoverflow.com/a/31237203/489916>
        $client->setApprovalPrompt('force');

        if ($request->session()->get('google_oauth_state') !== $request->get('state')) {
            abort(500, 'The session state did not match.');
        }

        $client->fetchAccessTokenWithAuthCode($request->get('code'));
        $accessToken = $client->getAccessToken();

        $client->setAccessToken($accessToken);
        $oauth2 = $clientFactory->make('Oauth2');
        $userinfo = (array)$oauth2->userinfo->get()->toSimpleObject();

        $yt = $clientFactory->make('YouTube');
        $res = $yt->channels->listChannels('brandingSettings,statistics', ['mine' => true]);
        $channel = $res[0]->toSimpleObject();

        $auth = GoogleAccount::firstOrNew([
            'id' => $userinfo['email'],
        ]);
        $auth->token_expiration = $client->getOAuth2Service()->getExpiresAt();
        $auth->token = $accessToken;
        $auth->userinfo = $userinfo;
        $auth->channel = $channel;
        $auth->save();

        return redirect('/');
    }

}
