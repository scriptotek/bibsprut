<?php

namespace App\Providers;

use App\WebdavClient;
use Illuminate\Support\ServiceProvider;
use Sabre\DAV\Client;

class WebdavServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        // $loader->alias('Vortex', 'App\Facades\Vortex');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('webdav', function ($app) {
            return new WebdavClient(new Client(config('webdav')));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('webdav');
    }

}
