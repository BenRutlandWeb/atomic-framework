<?php

namespace Atomic\Foundation;

use Illuminate\Container\Container;
use Atomic\Events\EventServiceProvider;
use Atomic\Routing\RoutingServiceProvider;
use Atomic\Support\ServiceProvider;

class Application extends Container
{
    /**
     * The application version
     */
    protected const VERSION = '0.1.0';

    /**
     * The application namespace
     *
     * @var string
     */
    protected $namespace = 'App\\';

    /**
     * The application base path
     *
     * @var string
     */
    protected $basePath;

    /**
     * All of the registered service providers.
     *
     * @var array
     */
    protected $serviceProviders = [];

    /**
     * Determine if the application has been booted
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Indicates if the application has been bootstrapped before.
     *
     * @var bool
     */
    protected $hasBeenBootstrapped = false;

    /**
     * Create an application instance
     *
     * @param string|null $basePath
     */
    public function __construct(?string $basePath = null)
    {
        if ($basePath) {
            $this->basePath = $basePath;
        }

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();
    }

    /**
     * Register the application base bindings
     *
     * @return void
     */
    public function registerBaseBindings(): void
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->instance(Container::class, $this);

        $this->singleton(Mix::class);
    }

    /**
     * Register the aplication service providers
     *
     * @return void
     */
    public function registerBaseServiceProviders(): void
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));
    }

    /**
     * Register the core aliases
     *
     * @return void
     */
    public function registerCoreContainerAliases(): void
    {
        $map = [
            'app'         => [
                self::class,
                \Illuminate\Contracts\Container\Container::class,
                \Psr\Container\ContainerInterface::class
            ],
            'auth'        => [\Atomic\Auth\AuthManager::class],
            'config'      => [\Atomic\Config\Repository::class],
            'console'     => [\Atomic\Console\Application::class],
            'events'      => [\Atomic\Events\Dispatcher::class],
            'files'       => [\Atomic\FileSystem\FileSystem::class],
            'hash'        => [\Atomic\Hashing\Hash::class],
            'mailer'      => [\Atomic\Mail\Mailer::class],
            'request'     => [\Atomic\Http\Request::class],
            'router'      => [\Atomic\Routing\Router::class],
            'router.ajax' => [\Atomic\Routing\AjaxRouter::class],
            'url'         => [\Atomic\Routing\UrlGenerator::class],
            'view'        => [\Atomic\View\View::class],
            'wp.action'   => [\Atomic\WordPress\Action::class],
            'wp.filter'   => [\Atomic\WordPress\Filter::class],
        ];

        foreach ($map as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    /**
     * Register a service provider
     *
     * @param \Atomic\Support\ServiceProvider|string $provider
     * @return \Atomic\Support\ServiceProvider
     */
    public function register($provider): ServiceProvider
    {
        if (($registered = $this->getProvider($provider))) {
            return $registered;
        }
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        $provider->register();

        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }

        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->markAsRegistered($provider);

        if ($this->isBooted()) {
            $this->bootProvider($provider);
        }

        return $provider;
    }

    /**
     * Get a provider
     *
     * @param \Atomic\Support\ServiceProvider|string $provider
     * @return \Atomic\Support\ServiceProvider|null
     */
    public function getProvider($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);
        return $this->serviceProviders[$name] ?? null;
    }

    /**
     * Return a service provider instance
     *
     * @param string $provider
     * @return \Atomic\Support\ServiceProvider
     */
    public function resolveProvider(string $provider): ServiceProvider
    {
        return new $provider($this);
    }

    /**
     * Add a service provider to the application
     *
     * @param \Atomic\Support\ServiceProvider $provider
     * @return void
     */
    protected function markAsRegistered(ServiceProvider $provider): void
    {
        $this->serviceProviders[get_class($provider)] = $provider;
    }

    /**
     * Boot the given service provider.
     *
     * @param \Atomic\Support\ServiceProvider $provider
     * @return mixed
     */
    protected function bootProvider(ServiceProvider $provider)
    {
        if (method_exists($provider, 'boot')) {
            return $this->call([$provider, 'boot']);
        }
    }

    /**
     * Get the service providers that have been loaded.
     *
     * @return array
     */
    public function getLoadedProviders(): array
    {
        return array_keys($this->serviceProviders);
    }

    /**
     * Register the providers specified in the app config
     *
     * @return void
     */
    public function registerConfiguredProviders(): void
    {
        foreach ($this['config']['app.providers'] as $provider) {
            $this->register($provider);
        }
    }

    /**
     * Determine if the application has booted.
     *
     * @return bool
     */
    public function isBooted(): bool
    {
        return $this->booted;
    }

    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->isBooted()) {
            return;
        }

        array_walk($this->serviceProviders, function (ServiceProvider $provider) {
            $this->bootProvider($provider);
        });

        $this->booted = true;
    }

    /**
     * Run the given array of bootstrap classes.
     *
     * @param  array  $bootstrappers
     * @return void
     */
    public function bootstrapWith(array $bootstrappers): void
    {
        $this->hasBeenBootstrapped = true;

        foreach ($bootstrappers as $bootstrapper) {
            $this->make($bootstrapper)->bootstrap($this);
        }
    }

    /**
     * Determine if the application has been bootstrapped before.
     *
     * @return bool
     */
    public function hasBeenBootstrapped(): bool
    {
        return $this->hasBeenBootstrapped;
    }

    /**
     * Return the application version
     *
     * @return string
     */
    public function version(): string
    {
        return static::VERSION;
    }

    /**
     * Return the base path
     *
     * @param string|null $path
     * @return string
     */
    public function basePath(?string $path = ''): string
    {
        return $this->basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the app namespace
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Determine if the application is running in the console
     *
     * @return bool
     */
    public function runningInConsole(): bool
    {
        return class_exists('WP_CLI');
    }
}
