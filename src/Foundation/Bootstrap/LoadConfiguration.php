<?php

namespace Atomic\Foundation\Bootstrap;

use Exception;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Atomic\Config\Repository;
use Atomic\Foundation\Application;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Atomic\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app): void
    {
        $app->instance('config', $config = new Repository([]));

        $this->loadConfigurationFiles($app, $config);
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Atomic\Foundation\Application  $app
     * @param  \Atomic\Config\Repository  $repository
     * @return void
     *
     * @throws \Exception
     */
    protected function loadConfigurationFiles(Application $app, Repository $repository): void
    {
        $files = $this->getConfigurationFiles($app);

        if (!isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Atomic\Foundation\Application  $app
     * @return array
     */
    protected function getConfigurationFiles(Application $app): array
    {
        $files = [];

        $configPath = realpath($app->basePath('config'));

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \SplFileInfo  $file
     * @param  string  $configPath
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, string $configPath): string
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }
}
