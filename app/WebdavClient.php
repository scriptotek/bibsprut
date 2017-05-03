<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use RuntimeException;
use Sabre\DAV\Client;

class WebdavClient extends Model
{
    protected $clients = [];

    /**
     * @param $inst
     * @return Client
     */
    protected function getClient($inst)
    {
        if (!isset($this->clients[$inst])) {
            $config = config('webdav');
            $config['baseUri'] = str_replace('{inst}', $inst, $config['baseUri']);
            $this->clients[$inst] = new Client($config);
        }

        return $this->clients[$inst];
    }

    protected function parseUri($uri)
    {
        if (!preg_match('/https?:\/\/(www\.)?([a-z]+)\.uio.no\/(.+)/', $uri, $matches)) {
            throw new RuntimeException('URL from unknown WebDAV domain: ' . $uri);
        }
        $inst = $matches[2];
        $path = $matches[3];

        return [$this->getClient($inst), $path];
    }

    public function get($url)
    {
        list($client, $path) = $this->parseUri($url);

        $response = $client->request('GET', $path);
        if ($response['statusCode'] == 401) {
            throw new RuntimeException('Please check WebDAV credentials. Got 401 Unauthorized');
        }
        $body = json_decode($response['body']);
        return $body;
    }

    public function put($url, $body)
    {
        list($client, $path) = $this->parseUri($url);

        $response = $client->request('PUT', $path, $body, [
            'Content-Type' => 'application/json',
        ]);

        if ($response['statusCode'] < 200 || $response['statusCode'] >= 300) {
            throw new RuntimeException('Failed to PUT ' . $path);
        };
    }
}
