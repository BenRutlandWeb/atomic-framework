<?php

namespace Atomic\Validation;

use Atomic\Foundation\Application;
use Atomic\Http\Request;
use Atomic\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('validator', function () {
            return new Validator();
        });

        $this->app->rebinding('request', function (Application $app, Request $request) {
            $request->setValidatorResolver(function () use ($app) {
                return $app['validator'];
            });
        });
    }
}
