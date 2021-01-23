<?php

namespace Atomic\Foundation\Http;

use Closure;
use Atomic\Foundation\Application;
use Atomic\Http\Request;
use Atomic\Support\Facades\Facade;
use Atomic\Support\Pipeline;
use Atomic\Routing\AjaxRouter;
use Atomic\Routing\Router;

class Kernel
{
    /**
     * The application implementation.
     *
     * @var \Atomic\Foundation\Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var \Atomic\Routing\Router
     */
    protected $router;

    /**
     * The router instance.
     *
     * @var \Atomic\Routing\AjaxRouter
     */
    protected $ajaxRouter;

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstrappers = [
        \Atomic\Foundation\Bootstrap\LoadConfiguration::class,
        \Atomic\Foundation\Bootstrap\RegisterFacades::class,
        \Atomic\Foundation\Bootstrap\RegisterProviders::class,
        \Atomic\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * The application's middleware stack.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * Create the HTTP kernel instance
     *
     * @param \Atomic\Foundation\Application $app
     * @param \Atomic\Routing\Router $router
     * @param \Atomic\Routing\AjaxRouter $router
     */
    public function __construct(Application $app, Router $router, AjaxRouter $ajaxRouter)
    {
        $this->app = $app;
        $this->router = $router;
        $this->ajaxRouter = $ajaxRouter;

        $this->syncMiddlewareToRouter();
    }

    /**
     * handle the request
     *
     * @param \Atomic\Http\Request $request
     * @return void
     */
    public function handle(Request $request): void
    {
        $this->bootstrap();

        $this->sendRequestThroughRouter($request);
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param  \Atomic\Http\Request  $request
     * @return void
     */
    protected function sendRequestThroughRouter(Request $request)
    {
        $this->app->instance('request', $request);

        Facade::clearResolvedInstance('request');

        return (new Pipeline($this->app))
            ->send($request)
            ->through($this->middleware)
            ->then($this->dispatchToRouter());
    }

    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        if (!$this->app->hasBeenBootstrapped()) {
            $this->app->bootstrapWith($this->bootstrappers());
        }
    }

    /**
     * Get the bootstrappers
     *
     * @return array
     */
    public function bootstrappers(): array
    {
        return $this->bootstrappers;
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter(): Closure
    {
        return function (Request $request) {
            $this->app->instance('request', $request);

            $this->ajaxRouter->dispatch($request);

            $this->app['events']->listen('rest_api_init', function () use ($request) {
                $this->router->dispatch($request);
            });
        };
    }

    /**
     * Sync the current state of the middleware to the router.
     *
     * @return void
     */
    protected function syncMiddlewareToRouter(): void
    {
        foreach ([$this->ajaxRouter, $this->router] as $router) {
            foreach ($this->middlewareGroups as $key => $middleware) {
                $router->middlewareGroup($key, $middleware);
            }

            foreach ($this->routeMiddleware as $key => $middleware) {
                $router->aliasMiddleware($key, $middleware);
            }
        }
    }
}
