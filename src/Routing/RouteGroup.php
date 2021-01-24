<?php

namespace Atomic\Routing;

class RouteGroup
{
    /**
     * Merge together the route groups
     *
     * @param array $new
     * @param array $old
     * @return array
     */
    public static function merge(array $new, array $old): array
    {
        return [
            'namespace'  => static::formatNamespace($old, $new),
            'prefix'     => static::formatPrefix($old, $new),
            'name'       => static::formatName($old, $new),
            'middleware' => static::formatMiddleware($old, $new),
        ];
    }

    /**
     * Format the route namespace
     *
     * @param array $old
     * @param array $new
     * @return string
     */
    public static function formatNamespace(array $old, array $new): string
    {
        return trim(($old['namespace'] ?? '') . '/' . ($new['namespace'] ?? ''), '/');
    }

    /**
     * Format the route prefix
     *
     * @param array $old
     * @param array $new
     * @return string
     */
    public static function formatPrefix(array $old, array $new): string
    {
        return trim(($old['prefix'] ?? '') . '/' . ($new['prefix'] ?? ''), '/');
    }

    /**
     * Format the route name
     *
     * @param array $old
     * @param array $new
     * @return string
     */
    public static function formatName(array $old, array $new): string
    {
        return ($old['name'] ?? '') . ($new['name'] ?? '');
    }

    /**
     * Format the route middleware
     *
     * @param array $old
     * @param array $new
     * @return array
     */
    public static function formatMiddleware(array $old, array $new): array
    {
        return array_merge_recursive($old['middleware'] ?? [], $new['middleware'] ?? []);
    }
}
