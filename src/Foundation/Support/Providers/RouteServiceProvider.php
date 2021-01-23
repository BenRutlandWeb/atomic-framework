<?php

namespace Atomic\Foundation\Support\Providers;

use Atomic\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The namespace for the REST routes.
     *
     * @var string|null
     */
    protected $namespace;

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutes();

        $this->app['router']->getRoutes()->refreshNameLookups();
        $this->app['router.ajax']->getRoutes()->refreshNameLookups();
    }


    /**
     * Load the application routes.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        if (method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }
    }
}
