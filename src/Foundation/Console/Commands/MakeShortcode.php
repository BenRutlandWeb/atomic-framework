<?php

namespace Atomic\Foundation\Console\Commands;

use Atomic\Console\GeneratorCommand;
use Atomic\Support\Str;

class MakeShortcode extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Shortcode';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:shortcode {name : The name of the shortcode}
                                           {--force : Overwrite the listener if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a shortcode';

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

        return str_replace('{{ tag }}', Str::snake($this->getNameInput()), $stub);
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/shortcode.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Shortcodes';
    }
}
