<?php

namespace Atomic\Foundation;

class Mix
{
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @return string
     */
    public function __invoke(string $path): string
    {
        $file = base_path(
            trim(app('config')->get('app.asset_url'), '/') . DIRECTORY_SEPARATOR . 'mix-manifest.json'
        );

        if (!file_exists($file)) {
            return asset($path, true);
        }

        $json = json_decode(app('files')->get($file));

        $path = '/' . trim($path, '/');

        return asset($json->$path ?? $path, true);
    }
}
