<?php

namespace Atomic\Routing;

use Atomic\Foundation\Application;
use Atomic\Support\ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{
    /**
     * Register the services
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('router', function (Application $app) {
            return new Router($app);
        });
        $this->app->singleton('router.ajax', function (Application $app) {
            return new AjaxRouter($app);
        });

        $this->app->singleton('url', function ($app) {

            $routes = $app['router']->getRoutes();
            $ajaxRoutes = $app['router.ajax']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            return new UrlGenerator(
                $routes,
                $ajaxRoutes,
                $app['request'],
                $app['config']['app.asset_url']
            );
        });
    }
}
