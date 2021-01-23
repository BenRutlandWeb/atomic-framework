<?php

namespace Atomic\Foundation\Console\Commands;

use Closure;
use ReflectionFunction;
use Atomic\Console\Command;

class ClosureCommand extends Command
{
    /**
     * The closure callback
     *
     * @var \Closure
     */
    protected $callback;

    /**
     * The closure callback
     *
     * @param string   $signature The command signature
     * @param \Closure $callback  The callback to run on the command
     */
    public function __construct(string $signature, Closure $callback)
    {
        $this->signature = $signature;
        $this->callback = $callback;

        parent::__construct();
    }

    /**
     * Handle the command call.
     *
     * @return void
     */
    protected function handle(): void
    {
        $inputs = array_merge($this->arguments(), $this->options());

        $parameters = [];

        foreach ((new ReflectionFunction($this->callback))->getParameters() as $parameter) {
            if (isset($inputs[$parameter->getName()])) {
                $parameters[$parameter->getName()] = $inputs[$parameter->getName()];
            }
        }

        $callback = $this->callback->bindTo($this, $this);

        call_user_func_array($callback, $parameters);
    }

    /**
     * Add a description to the command
     *
     * @param string $description
     * @return void
     */
    public function describe(string $description): void
    {
        $this->description = $description;
    }
}
