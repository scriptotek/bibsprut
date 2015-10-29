<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sabre\DAV\Client;

class WebdavClient extends Model
{

    protected $client;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get($url)
    {
        $rest = preg_replace('/https?:\/\/.*?\//', '', $url);
        $response = $this->client->request('GET', $rest);

        return json_decode($response['body']);
    }
}
