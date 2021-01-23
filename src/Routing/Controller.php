<?php

namespace Atomic\Routing;

use BadMethodCallException;

abstract class Controller
{
    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return void
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $parameters): void
    {
        throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
    }
}
