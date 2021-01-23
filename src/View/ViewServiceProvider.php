<?php

namespace Atomic\View;

use Atomic\Foundation\Application;
use Atomic\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('view', function (Application $app) {
            return new View($app['config']['view.path']);
        });

        $this->app->singleton('view.redirect', function (Application $app) {
            return new TemplateRedirect($app, $app['events']);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->make('view.redirect');
    }
}
