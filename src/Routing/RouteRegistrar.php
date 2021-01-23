<?php

namespace Atomic\Routing;

use BadMethodCallException;
use Closure;
use InvalidArgumentException;

class RouteRegistrar
{
    /**
     * The router instance
     *
     * @var \Atomic\Routing\Router
     */
    protected $router;

    /**
     * The group attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The methods to pass to the router
     *
     * @var array
     */
    protected $passThroughMethods = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'any', 'match'
    ];

    /**
     * The allowed attributes for the route group
     *
     * @var array
     */
    protected $allowedAttributes = [
        'namespace', 'prefix', 'name', 'middleware',
    ];

    /**
     * Construct the router registrar
     *
     * @param \Atomic\Routing\Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Set an attribute
     *
     * @param string $key
     * @param mixed $value
     * @return \Atomic\Routing\RouteRegistrar
     *
     * @throws \InvalidArgumentException
     */
    public function attribute(string $key, $value): RouteRegistrar
    {
        if (!in_array($key, $this->allowedAttributes)) {
            throw new InvalidArgumentException("Attribute [{$key}] does not exist.");
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Register a route on the router
     *
     * @param string $method
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    protected function registerRoute(string $method, string $uri, $action = null): Route
    {
        return $this->router->{$method}($uri, $action);
    }

    /**
     * Pass the group attributes to the router
     *
     * @param \Closure|string $callback
     * @return void
     */
    public function group($callback): void
    {
        $this->router->group($this->attributes, $callback);
    }

    /**
     * Dynamically call the registrar to handle adding attributes
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $parameters = [])
    {
        if (in_array($method, $this->passThroughMethods)) {
            return $this->registerRoute($method, ...$parameters);
        }
        if (in_array($method, $this->allowedAttributes)) {
            if ($method === 'middleware') {
                return $this->attribute($method, is_array($parameters[0]) ? $parameters[0] : $parameters);
            }
            return $this->attribute($method, $parameters[0]);
        }

        throw new BadMethodCallException(sprintf('Method %s::%s does not exist.', static::class, $method));
    }
}
