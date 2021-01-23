<?php

namespace Atomic\Support;

use Atomic\Foundation\Application;

abstract class ServiceProvider
{
    /**
     * The application instance
     *
     * @var \Atomic\Foundation\Application
     */
    protected $app;

    /**
     * Create the service provider
     *
     * @param \Atomic\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register the services
     *
     * @return void
     */
    public function register(): void
    {
        # code...
    }
}
