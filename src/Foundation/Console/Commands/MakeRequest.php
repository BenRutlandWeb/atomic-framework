<?php

namespace Atomic\Foundation\Console\Commands;

use Atomic\Console\GeneratorCommand;

class MakeRequest extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'FormRequest';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:request {name : The name of the form request}
                                         {--force : Overwrite the request if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a form request';

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/request.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\\Http\\Requests';
    }
}
