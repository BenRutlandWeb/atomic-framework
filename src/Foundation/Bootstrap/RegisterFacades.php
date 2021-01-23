<?php

namespace Atomic\Foundation\Bootstrap;

use Atomic\Foundation\AliasLoader;
use Atomic\Foundation\Application;
use Atomic\Support\Facades\Facade;

class RegisterFacades
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Atomic\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        Facade::clearResolvedInstances();

        Facade::setFacadeApplication($app);

        AliasLoader::getInstance(
            $app['config']->get('app.aliases', []),
        )->register();
    }
}
