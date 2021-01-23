<?php

namespace Atomic\Foundation\Bootstrap;

use Atomic\Foundation\Application;

class BootProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Atomic\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->boot();
    }
}
