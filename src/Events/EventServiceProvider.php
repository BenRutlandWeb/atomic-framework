<?php

namespace Atomic\Events;

use Atomic\Foundation\Application;
use Atomic\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register the services
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('events', function (Application $app) {
            return new Dispatcher($app);
        });
    }
}
