<?php

namespace Atomic\Http;

use Atomic\Http\FormRequest;
use Atomic\Support\ServiceProvider;

class FormRequestServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->app->afterResolving(FormRequest::class, function ($resolved) {
            return $resolved->validateResolved();
        });

        $this->app->resolving(FormRequest::class, function ($request, $app) {
            return FormRequest::createFrom($app['request'], $request);
        });
    }
}
