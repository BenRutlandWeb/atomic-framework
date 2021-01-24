<?php

namespace Atomic\WordPress;

use ReflectionClass;
use Atomic\Foundation\Application;
use Atomic\Support\Arr;
use Atomic\Support\Str;
use Atomic\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class WordPressServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('wp.action', function (Application $app) {
            return new Action($app['events']);
        });
        $this->app->singleton('wp.filter', function (Application $app) {
            return new Filter($app['events']);
        });
    }

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->load(base_path('app/Cpts'));
    }

    /**
     * Load the post types automatically without needing to be registered.
     *
     * @param string|array $paths
     *
     * @return void
     */
    protected function load($paths): void
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->app->getNamespace();

        foreach ((new Finder())->in($paths)->files() as $postType) {
            $postType = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($postType->getPathname(), realpath(base_path('app')) . '/')
            );

            if (
                is_subclass_of($postType, Cpt::class) &&
                !(new ReflectionClass($postType))->isAbstract()
            ) {
                $this->app->make($postType);
            }
        }
    }
}
