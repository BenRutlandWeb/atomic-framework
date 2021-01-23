<?php

namespace Atomic\Filesystem;

use Atomic\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('files', function () {
            return new Filesystem();
        });
    }
}
