<?php

namespace Atomic\Routing;

use Closure;
use Throwable;
use Illuminate\Container\Container;
use Atomic\Foundation\Exceptions\ExceptionHandler;
use Atomic\Http\Request;
use Atomic\Support\Pipeline;
use WP_REST_Request;

class Router
{
    /**
     * The routes to register
     *
     * @var \Atomic\Routing\RouteCollection
     */
    protected $routes = [];

    /**
     * The group stack
     *
     * @var array
     */
    protected $groupStack = [];

    /**
     * The container
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * All of the short-hand keys for middlewares.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * All of the middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * Create the router instance
     *
     * @param \Illuminate\Container\Container|null $container
     */
    public function __construct(Container $container = null)
    {
        $this->routes = new RouteCollection();
        $this->container = $container ?? new Container();
    }

    /**
     * Add a GET route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function get(string $uri, $action): Route
    {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Add a POST route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function post(string $uri, $action): Route
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Add a PUT route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function put(string $uri, $action): Route
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Add a PATCH route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function patch(string $uri, $action): Route
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Add a DELETE route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function delete(string $uri, $action): Route
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Add an OPTIONS route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function options(string $uri, $action): Route
    {
        return $this->addRoute('OPTIONS', $uri, $action);
    }

    /**
     * Add an ANY route
     *
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function any(string $uri, $action): Route
    {
        return $this->addRoute(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $uri, $action);
    }

    /**
     * Add an MATCHES route
     *
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function matches($methods, string $uri, $action): Route
    {
        return $this->addRoute($methods, $uri, $action);
    }

    /**
     * Add a route to the route collection
     *
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function addRoute($methods, string $uri, $action): Route
    {
        return $this->routes->add($this->createRoute($methods, $uri, $action));
    }

    /**
     * Create a route
     *
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function createRoute($methods, string $uri, $action): Route
    {
        $route = $this->newRoute($methods, $this->prefix($uri), $action);

        if ($this->hasGroupStack()) {
            $this->mergeGroupAttributesIntoRoute($route);
        }

        return $route;
    }

    /**
     * Make a new route instance
     *
     * @param array|string $methods
     * @param string $uri
     * @param mixed $action
     * @return \Atomic\Routing\Route
     */
    public function newRoute($methods, string $uri, $action): Route
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this);
    }

    /**
     * Dispatch the routes to the WordPress REST route register
     *
     * @param \Atomic\Routing\Request $request
     * @return void
     */
    public function dispatch(Request $request): void
    {
        $this->routes->each(function (Route $route) use ($request) {
            $this->registerRoute($request, $route);
        });
    }

    /**
     * Register the route with the WordPress REST API
     *
     * @param \Atomic\Http\Request $request
     * @param \Atomic\Routing\Route $route
     * @return void
     */
    public function registerRoute(Request $request, Route $route): void
    {
        register_rest_route($route->namespace(), $this->parseUri($route), [
            'methods'  => $route->methods(),
            'callback' => $this->runRouteWithinStack($route, $request),
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Parse the route URI
     *
     * @param  \Atomic\Routing\Route $route  $route
     * @return string
     */
    protected function parseUri(Route $route): string
    {
        return preg_replace('@\/\{([\w]+?)(\?)?\}@', '\/?(?P<$1>[a-zA-Z0-9-]+)$2', $route->uri());
    }

    /**
     * Run the route action passing through the request
     *
     * @param \Atomic\Routing\Route $route
     * @param \Atomic\Http\Request $request
     * @return \Closure
     */
    public function runRouteWithinStack(Route $route, Request $request): Closure
    {
        return function (WP_REST_Request $wpRequest) use ($route, $request) {

            $request->setRouteResolver(function () use ($route) {
                return $route;
            });
            try {
                return (new Pipeline($this->container))
                    ->send($request->merge($wpRequest->get_url_params()))
                    ->through($this->gatherRouteMiddleware($route))
                    ->then(function ($request) use ($route) {

                        $this->container->instance('request', $request);

                        return $this->container->call($route->action());
                    });
            } catch (Throwable $e) {
                die((new ExceptionHandler($e))->handle());
            }
        };
    }

    /**
     * Gather the middleware for the given route with resolved class names.
     *
     * @param  \Atomic\Routing\Route  $route
     * @return array
     */
    public function gatherRouteMiddleware(Route $route)
    {
        return collect($route->gatherMiddleware())
            ->map(function ($name) {
                return (array) MiddlewareNameResolver::resolve($name, $this->middleware, $this->middlewareGroups);
            })
            ->flatten()
            ->values()
            ->all();
    }

    /**
     * Register a group of middleware.
     *
     * @param  string  $name
     * @param  array  $middleware
     * @return $this
     */
    public function middlewareGroup(string $name, array $middleware): self
    {
        $this->middlewareGroups[$name] = $middleware;

        return $this;
    }

    /**
     * Register a short-hand name for a middleware.
     *
     * @param  string  $name
     * @param  string  $class
     * @return $this
     */
    public function aliasMiddleware(string $name, string $class): self
    {
        $this->middleware[$name] = $class;

        return $this;
    }

    /**
     * Update the group attributes and load the grouped routes
     *
     * @param array $attributes
     * @param \Closure|string $routes
     * @return void
     */
    public function group(array $attributes, $routes): void
    {
        $this->updateGroupStack($attributes);

        $this->loadRoutes($routes);

        array_pop($this->groupStack);
    }

    /**
     * Load the routes
     *
     * @param \Closure|string $routes
     * @return void
     */
    public function loadRoutes($routes): void
    {
        if ($routes instanceof Closure) {
            $routes($this);
        } else {
            $router = $this;

            require $routes;
        }
    }

    /**
     * Determine if there is a group stack
     *
     * @return bool
     */
    public function hasGroupStack(): bool
    {
        return !empty($this->groupStack);
    }

    /**
     * Update the group stack
     *
     * @param array $attributes
     * @return void
     */
    protected function updateGroupStack(array $attributes): void
    {
        if ($this->hasGroupStack()) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }

        $this->groupStack[] = $attributes;
    }

    /**
     * Merge a group with the previous group
     *
     * @param array $new
     * @return array
     */
    public function mergeWithLastGroup(array $new): array
    {
        return RouteGroup::merge($new, end($this->groupStack));
    }

    /**
     * Merge the last group attributes and set on the route
     *
     * @param \Atomic\Routing\Route $route
     * @return void
     */
    protected function mergeGroupAttributesIntoRoute(Route $route): void
    {
        $route->setAttributes($this->mergeWithLastGroup(
            $route->getAttributes()
        ));
    }

    /**
     * Get the prefix from the last group added
     *
     * @return string
     */
    public function getLastGroupPrefix(): string
    {
        if ($this->hasGroupStack()) {
            $last = end($this->groupStack);

            return $last['prefix'] ?? '';
        }
        return '';
    }

    /**
     * Get the prefixed URI
     *
     * @param string $uri
     * @return string
     */
    protected function prefix(string $uri): string
    {
        return trim(trim($this->getLastGroupPrefix(), '/') . '/' . trim($uri, '/'), '/') ?: '/';
    }

    /**
     * Get the router route colelction
     *
     * @return \Atomic\Routing\RouteCollection
     */
    public function getRoutes(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * Return the route URL
     *
     * @param \Atomic\Routing\Route $route
     * @return string
     */
    public function routeUrl(Route $route): string
    {
        return $this->container['url']->rest($route->namespace() . '/' . $route->uri());
    }

    /**
     * Dynamically call the route registrar
     *
     * @param string $method
     * @param array $parameters
     * @return \Atomic\Routing\RouteRegistrar
     */
    public function __call(string $method, array $parameters = []): RouteRegistrar
    {
        if ($method === 'middleware') {
            return (new RouteRegistrar($this))->attribute(
                $method,
                is_array($parameters[0]) ? $parameters[0] : $parameters
            );
        }
        return (new RouteRegistrar($this))->attribute($method, ...$parameters);
    }
}
