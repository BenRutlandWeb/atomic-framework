<?php

namespace Atomic\Console;

use Atomic\Foundation\Application as BaseApplication;
use Atomic\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The pre-built commands
     *
     * @var array
     */
    protected $commands = [
        'command.make.command',
        'command.make.controller',
        'command.make.cpt',
        'command.make.event',
        'command.make.listener',
        'command.make.mail',
        'command.make.middleware',
        'command.make.provider',
        'command.make.request',
        'command.make.rule',
        'command.make.shortcode',
        'command.make.subscriber',
        'command.make.taxonomy',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('console', function (BaseApplication $app) {
            return new Application($app);
        });

        $this->app->singleton('command.make.command', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeCommand($app['files']);
        });
        $this->app->singleton('command.make.controller', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeController($app['files']);
        });
        $this->app->singleton('command.make.cpt', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeCpt($app['files']);
        });
        $this->app->singleton('command.make.event', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeEvent($app['files']);
        });
        $this->app->singleton('command.make.listener', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeListener($app['files']);
        });
        $this->app->singleton('command.make.mail', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeMail($app['files']);
        });
        $this->app->singleton('command.make.middleware', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeMiddleware($app['files']);
        });
        $this->app->singleton('command.make.provider', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeProvider($app['files']);
        });
        $this->app->singleton('command.make.request', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeRequest($app['files']);
        });
        $this->app->singleton('command.make.rule', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeRule($app['files']);
        });
        $this->app->singleton('command.make.shortcode', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeShortcode($app['files']);
        });
        $this->app->singleton('command.make.subscriber', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeSubscriber($app['files']);
        });
        $this->app->singleton('command.make.taxonomy', function ($app) {
            return new \Atomic\Foundation\Console\Commands\MakeTaxonomy($app['files']);
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }
        $console = $this->app->make('console');

        foreach ($this->commands as $command) {
            $console->add($this->app->make($command));
        }

        $console->load(base_path('Console/Commands'));

        require base_path('routes/console.php');

        $console->boot();
    }
}
