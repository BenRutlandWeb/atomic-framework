<?php

namespace Atomic\Support\Facades;

class Config extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'config';
    }
}
