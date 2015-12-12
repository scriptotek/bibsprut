<?php

namespace App\Providers;

use Illuminate\Filesystem\FileSystemManager;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use Sabre\DAV\Client as DavClient;

class AppFileSystemManager extends FileSystemManager
{

    /**
     * Create an instance of the WebDav driver.
     *
     * @param  array  $config
     * @return \Illuminate\Contracts\Filesystem\Cloud
     */
    public function createWebdavDriver(array $config)
    {

        $client = new DavClient($config);

        return $this->adapt(
            new Flysystem(new WebDAVAdapter($client))
        );

//        $client = new Rackspace($config['endpoint'], [
//            'username' => $config['username'], 'apiKey' => $config['key'],
//        ]);
//
//        return $this->adapt(new Flysystem(
//            new RackspaceAdapter($this->getRackspaceContainer($client, $config))
//        ));
    }
}
