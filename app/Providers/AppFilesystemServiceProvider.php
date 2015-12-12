<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemServiceProvider;

class AppFilesystemServiceProvider extends FilesystemServiceProvider
{
    /**
     * Register the filesystem manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('filesystem', function () {
            return new AppFilesystemManager($this->app);
        });
    }
}
