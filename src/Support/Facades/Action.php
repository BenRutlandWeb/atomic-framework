<?php

namespace Atomic\Support\Facades;

class Action extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'wp.action';
    }
}
