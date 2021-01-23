<?php

namespace Atomic\Foundation\Console\Commands;

use Atomic\Console\GeneratorCommand;

class MakeListener extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Listener';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:listener {name : The name of listener}
                                          {--event : The name of the event}
                                          {--force : Overwrite the listener if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a listener';

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass(string $stub, string $name): string
    {
        $stub = parent::replaceClass($stub, $name);

        return str_replace('{{ event }}', $this->option('event'), $stub);
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        if ($this->option('event')) {
            return __DIR__ . '/stubs/listener.event.stub';
        }
        return __DIR__ . '/stubs/listener.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Listeners';
    }
}
