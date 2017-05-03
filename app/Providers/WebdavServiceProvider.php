<?php

namespace App\Providers;

use App\WebdavClient;
use Illuminate\Support\ServiceProvider;
use Sabre\DAV\Client;

class WebdavServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WebdavClient::class, function () {
            return new WebdavClient();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            WebdavClient::class,
        ];
    }

}
