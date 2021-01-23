<?php

namespace Atomic\Routing;

class Route
{
    /**
     * The route methods
     *
     * @var array
     */
    protected $methods;

    /**
     * The route URI
     *
     * @var string
     */
    protected $uri;

    /**
     * The route action
     *
     * @var Closure|callable|string
     */
    protected $action;

    /**
     * The router
     *
     * @var \Atomic\Routing\Router
     */
    protected $router;

    /**
     * The route attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The route midleware
     *
     * @var array
     */
    protected $computedMiddleware;

    /**
     * Create a route instance
     *
     * @param array|string $methods
     * @param string $uri
     * @param \Closure|callable|string $action
     */
    public function __construct($methods, string $uri, $action)
    {
        $this->methods = (array) $methods;
        $this->uri = $uri;
        $this->action = $action;

        if (in_array('GET', $this->methods) && !in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }
    }

    /**
     * The route namespace
     *
     * @return string
     */
    public function namespace(): string
    {
        return $this->attributes['namespace'];
    }

    /**
     * Set the route name, appended if a group name exists
     *
     * @param string $name
     * @return self
     */
    public function name(string $name): self
    {
        $this->attributes['name'] = ($this->attributes['name'] ?? '') . $name;

        return $this;
    }

    /**
     * Get the route name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    /**
     * The route methods
     *
     * @return array
     */
    public function methods(): array
    {
        return $this->methods;
    }

    /**
     * The route URI
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * The route action
     *
     * @return \Closure|callable|string
     */
    public function action()
    {
        if (is_array($this->action)) {
            return "{$this->action[0]}@{$this->action[1]}";
        }

        return $this->action;
    }

    /**
     * Set the route attributes
     *
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * Get the route attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Get all middleware
     *
     * @return array
     */
    public function gatherMiddleware(): array
    {
        if (!is_null($this->computedMiddleware)) {
            return $this->computedMiddleware;
        }

        return $this->computedMiddleware = array_unique($this->middleware(), SORT_REGULAR);
    }

    /**
     * Get or set the middlewares attached to the route.
     *
     * @param  array|string|null  $middleware
     * @return self|array
     */
    public function middleware($middleware = null)
    {
        if (is_null($middleware)) {
            return (array) ($this->attributes['middleware'] ?? []);
        }

        $this->attributes['middleware'] = array_merge(
            (array) ($this->attributes['middleware'] ?? []),
            (array) $middleware
        );

        return $this;
    }

    /**
     * Set the router instance
     *
     * @param \Atomic\Routing\Router $router
     * @return self
     */
    public function setRouter(Router $router): self
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Get the route URL
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->router->routeUrl($this);
    }
}
