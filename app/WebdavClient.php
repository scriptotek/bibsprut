<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sabre\DAV\Client;

class WebdavClient extends Model
{

    protected $client;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function getClientFromInst($inst)
    {
        $config = config('webdav');
        $config['baseUri'] = str_replace('{inst}', $inst, $config['baseUri']);
        return new Client($config);
    }

    public function get($url)
    {
        if (preg_match('/https?:\/\/(www\.)?([a-z]+)\.uio.no\/(.+)/', $url, $matches)) {
            $client = $this->getClientFromInst($matches[2]);
            $rest = $matches[3];
            $response = $client->request('GET', $rest);
            $response = json_decode($response['body']);
            if (!$response) {
                return null;
            }
            $response->inst = $matches[2];

            return $response;
        } else {
            return null;  // Eller exception?
        }
    }

    public function put($url, $body)
    {
        $response = $this->client->request('PUT', $url, $body);
        return ($response['statusCode'] == 201);
    }
}
