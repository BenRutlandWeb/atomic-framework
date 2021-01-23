<?php

namespace Atomic\Console;

use Closure;
use ReflectionClass;
use Atomic\Foundation\Console\Commands\ClosureCommand;
use Atomic\Foundation\Application as BaseApplication;
use Atomic\Support\Arr;
use Atomic\Support\Str;
use Symfony\Component\Finder\Finder;

class Application
{
    /**
     * The registered commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * The app instance
     *
     * @var \Atomic\Foundation\Application
     */
    protected $app;

    /**
     * Bind the application instance to console application
     *
     * @param \Atomic\Foundation\Application $app
     */
    public function __construct(BaseApplication $app)
    {
        $this->app = $app;
    }

    /**
     * Register a closure command.
     *
     * @param  string  $signature The command signature
     * @param  \Closure $callback  The callback to run
     * @return mixed
     */
    public function command(string $signature, Closure $callback)
    {
        $command = new ClosureCommand($signature, $callback);

        $this->add($command);

        return $command;
    }

    /**
     * Add a command to the application.
     *
     * @param \Atomic\Console\Command $command
     * @return void
     */
    public function add(Command $command): void
    {
        $command->setApplication($this->app);

        $this->commands[] = $command;
    }

    /**
     * Register each of the commands
     *
     * @return void
     */
    public function boot(): void
    {
        foreach ($this->commands as $command) {
            $command->boot();
        }
    }

    /**
     * Load the console commands
     *
     * @param array|string $paths
     * @return void
     */
    public function load($paths): void
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->app->getNamespace();

        foreach ((new Finder)->in($paths)->files() as $command) {
            $command = $namespace . str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($command->getPathname(), realpath(base_path('app')) . DIRECTORY_SEPARATOR)
            );

            if (
                is_subclass_of($command, Command::class) &&
                !(new ReflectionClass($command))->isAbstract()
            ) {
                $this->add($this->app->make($command));
            }
        }
    }
}
