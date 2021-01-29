<?php

use Atomic\Auth\AuthManager;
use Atomic\Foundation\Application;
use Atomic\Foundation\Mix;
use Atomic\Support\Collection;

if (!function_exists('ajax_route')) {
    /**
     * Generate the URL to a named ajax route.
     *
     * @param  array|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function ajax_route($name, $parameters = [], bool $absolute = true): string
    {
        return app('url')->ajaxRoute($name, $parameters, $absolute);
    }
}

if (!function_exists('app')) {
    /**
     * Get the available application instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return \Atomic\Foundation\Application|mixed
     */
    function app(?string $abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Application::getInstance();
        }

        return Application::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('asset')) {
    /**
     * Return an asset URL
     *
     * @return string
     */
    function asset(string $path): string
    {
        return app('url')->asset($path);
    }
}

if (!function_exists('auth')) {
    /**
     * Get the auth object.
     *
     * @return \Atomic\Auth\AuthManager
     */
    function auth(): AuthManager
    {
        return app('auth');
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the application base path
     *
     * @param string|null $path
     * @return string
     */
    function base_path(?string $path = ''): string
    {
        return app()->basePath($path);
    }
}

if (!function_exists('collect')) {
    /**
     * Get a collection
     *
     * @param mixed $items
     * @return \Atomic\Support\Collection
     */
    function collect($items = null): Collection
    {
        return new Collection($items);
    }
}

if (!function_exists('config')) {
    /**
     * Get an item from the config
     *
     * @param string $key
     * @return mixed
     */
    function config(string $key)
    {
        return app('config')->get($key);
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Return the csrf field.
     *
     * @return string
     */
    function csrf_field(): string
    {
        return wp_nonce_field('_token', '_token', true, false);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Return the csrf token.
     *
     * @return string
     */
    function csrf_token(): string
    {
        return wp_create_nonce('_token');
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     *
     * @param mixed|null ...$data
     * @return void
     */
    function dd(...$data): void
    {
        die(var_dump(...$data));
    }
}

if (!function_exists('dump')) {
    /**
     * Dump
     *
     * @param mixed|null ...$data
     * @return void
     */
    function dump(...$data): void
    {
        var_dump(...$data);
    }
}

if (!function_exists('env')) {
    /**
     * Get an envorinment variable
     *
     * @param string $const
     * @param mixed $default
     * @return mixed
     */
    function env(string $const, $default = null)
    {
        return defined($const) ? constant($const) : $default;
    }
}

if (!function_exists('event')) {
    /**
     * Dispatch an event and call the listeners.
     *
     * @param mixed $args
     * @return mixed
     */
    function event(...$args)
    {
        return app('events')->dispatch(...$args);
    }
}

if (!function_exists('method_field')) {
    /**
     * Return the method field.
     *
     * @param string $method The HTTP method
     * @return string
     */
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . esc_attr($method) . '" />';
    }
}

if (!function_exists('mix')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @return string
     */
    function mix(string $path): string
    {
        return app(Mix::class)($path);
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to another url
     *
     * @param string $url
     * @param int $status
     * @return void
     */
    function redirect(string $url, int $status = 302): void
    {
        app('url')->redirect($url, $status);
    }
}

if (!function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param  array|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function route($name, $parameters = [], bool $absolute = true): string
    {
        return app('url')->route($name, $parameters, $absolute);
    }
}

if (!function_exists('svg')) {
    /**
     * Return the contents of an SVG
     *
     * @param string $path
     * @return string
     */
    function svg(string $path): string
    {
        $file = base_path(trim(config('app.asset_url'), '/') . "/{$path}.svg");

        if (app('files')->exists($file)) {
            return app('files')->get($file);
        }
        return '';
    }
}

if (!function_exists('url')) {
    /**
     * Return the UrlGenerator instance
     *
     * @param string $path
     * @return \Atomic\Routing\UrlGenerator|string
     */
    function url(string $path = '')
    {
        if ($path) {
            return app('url')->home($path);
        }
        return app('url');
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('view')) {
    /**
     * Return a view
     *
     * @param string $view
     * @param array $args
     * @return string
     */
    function view(string $view, array $args = []): string
    {
        return (string) app('view')->make($view, $args);
    }
}

if (!function_exists('windows_os')) {
    /**
     * Determine whether the current environment is Windows based.
     *
     * @return bool
     */
    function windows_os(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}
