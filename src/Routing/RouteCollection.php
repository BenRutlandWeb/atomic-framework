<?php

namespace Atomic\Routing;

use Atomic\Support\Collection;

class RouteCollection extends Collection
{
    /**
     * The named routes list
     *
     * @var array
     */
    protected $nameList = [];

    /**
     * Return the route rather than the collection
     *
     * @param \Atomic\Routing\Route $route
     * @return \Atomic\Routing\Route
     */
    public function add($route)
    {
        parent::add($route);

        return $route;
    }

    /**
     * Determine if the route collection contains a given named route.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasNamedRoute(string $name): bool
    {
        return !is_null($this->getByName($name));
    }

    /**
     * Get a route instance by its name.
     *
     * @param  string  $name
     * @return \Atomic\Routing\Route|null
     */
    public function getByName(string $name): ?Route
    {
        return $this->nameList[$name] ?? null;
    }

    /**
     * Refresh the name look-up table.
     *
     * This is done in case any names are fluently defined or if routes are overwritten.
     *
     * @return void
     */
    public function refreshNameLookups(): void
    {
        $this->nameList = [];

        $this->each(function (Route $route) {
            if ($name = $route->getName()) {
                $this->nameList[$name] = $route;
            }
        });
    }
}
