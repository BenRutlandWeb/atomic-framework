<?php

namespace Atomic\Support\Facades;

class View extends Facade
{
    /**
     * Get the name of the component
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return 'view';
    }
}
