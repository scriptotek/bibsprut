<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PulkitJalan\Google\Client as GoogleClient;

class GoogleAccount extends Model
{

    public $incrementing = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'token' => 'array',
        'userinfo' => 'array',
        'channel' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id'];

    public function getClient()
    {
        $client = new GoogleClient(config('google'));

        // Per https://developers.google.com/identity/protocols/OAuth2WebServer
        $client->setAccessType('offline');

        // To get a refresh token, per <http://stackoverflow.com/a/31237203/489916>
        $client->setApprovalPrompt('force');

        $client->setAccessToken($this->token);

        return $client;
    }

    public function recordings()
    {
        return $this->belongsToMany('App\Recording');
    }
}
