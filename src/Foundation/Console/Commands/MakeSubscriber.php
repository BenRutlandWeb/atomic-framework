<?php

namespace Atomic\Foundation\Console\Commands;

use Atomic\Console\GeneratorCommand;

class MakeSubscriber extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Subscriber';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:subscriber {name : The name of the subscriber}
                                            {--force : Overwrite the listener if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make an event subscriber';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/subscriber.stub';
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
