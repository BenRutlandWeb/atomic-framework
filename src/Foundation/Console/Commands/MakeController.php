<?php

namespace Atomic\Foundation\Console\Commands;

use Atomic\Console\GeneratorCommand;

class MakeController extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:controller {name : The name of the controller}
                                            {--resource : Generate a resource controller class}
                                            {--force : Overwrite the controller if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a controller';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        if ($this->option('resource')) {
            return __DIR__ . '/stubs/controller.resource.stub';
        }
        return __DIR__ . '/stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Http\Controllers';
    }
}
