<?php

namespace Atomic\Auth;

use Atomic\Foundation\Application;
use Atomic\Http\Request;
use Atomic\Support\ServiceProvider;
use WP_User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('auth', function () {
            return new AuthManager();
        });

        $this->app->bind('auth.user', function (Application $app) {
            return $app['auth']->user();
        });

        $this->app->rebinding('request', function (Application $app, Request $request) {
            $request->setUserResolver(function () use ($app) {
                return $app['auth']->user();
            });
        });
    }
}
