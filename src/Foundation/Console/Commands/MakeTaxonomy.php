<?php

namespace Atomic\Foundation\Console\Commands;

use Atomic\Console\GeneratorCommand;
use Atomic\Support\Str;

class MakeTaxonomy extends GeneratorCommand
{
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Taxonomy';

    /**
     * The command signature.
     *
     * @var string
     */
    protected $signature = 'make:taxonomy {name : The taxonomy name}
                                          {--force : Overwrite the taxonomy if it exists}';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Make a taxonomy';

    /**
     * Handle making the model
     *
     * @param string $name
     * @return void
     */
    protected function handle(): void
    {
        parent::handle();

        flush_rewrite_rules();
    }

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

        [$public, $hierarchical] = $this->userFeedback();

        return str_replace(
            ['{{ name }}', '{{ public }}', '{{ hierarchical }}'],
            [Str::lower($this->argument('name')), $public, $hierarchical],
            $stub
        );
    }

    /**
     * Ask a series of questions
     *
     * @return array
     */
    protected function userFeedback(): array
    {
        return [
            $this->confirm('Is public:') ? 'true' : 'false',
            $this->confirm('Is hierarchical:') ? 'true' : 'false',
        ];
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/taxonomy.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . '\Cpts\\Taxonomies';
    }
}
