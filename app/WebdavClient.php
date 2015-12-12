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
     * @param Client $client
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

    public function put($url, $body)
    {
        $response = $this->client->request('PUT', $url, $body);
        return ($response['statusCode'] == 201);
    }
}
