<?php

namespace Atomic\Support\Facades;

class Route extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'router';
    }
}
