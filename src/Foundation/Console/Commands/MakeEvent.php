<?php

namespace Atomic\Foundation\Console\Commands;

use Atomic\Console\GeneratorCommand;

class MakeEvent extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Event';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:event {name : The name of event}
                                       {--force : Overwrite the event if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make an event';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/event.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Events';
    }
}
