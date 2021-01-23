<?php

namespace Atomic\Routing;

use Closure;
use JsonSerializable;
use Atomic\Contracts\Support\Renderable;
use Atomic\Http\Request;
use Atomic\Support\Pipeline;

class AjaxRouter extends Router
{
    /**
     * Register the route with the WordPress REST API
     *
     * @param \Atomic\Http\Request $request
     * @param \Atomic\Routing\Route $route
     * @return void
     */
    public function registerRoute(Request $request, Route $route): void
    {
        if (in_array($request->method(), $route->methods())) {
            $this->container['events']->listen(
                $this->getAjaxActions($route->uri()),
                $this->runRouteWithinStack($route, $request)
            );
        }
    }

    /**
     * Return an array of AJAX actions
     *
     * @param string $action
     * @return array
     */
    protected function getAjaxActions(string $action): array
    {
        return ["wp_ajax_nopriv_{$action}", "wp_ajax_{$action}"];
    }

    /**
     * Run the route action passing through the request
     *
     * @param \Atomic\Routing\Route $route
     * @param \Atomic\Http\Request $request
     * @return \Closure
     */
    public function runRouteWithinStack(Route $route, Request $request): \Closure
    {
        return function () use ($route, $request) {

            $request->setRouteResolver(function () use ($route) {
                return $route;
            });

            $response = (new Pipeline($this->container))
                ->send($request)
                ->through($this->gatherRouteMiddleware($route))
                ->then(function ($request) use ($route) {
                    return $this->prepareResponse(
                        $request,
                        $this->container->call($route->action(), ['request' => $request])
                    );
                });
            die($response);
        };
    }

    /**
     * prepare the response.
     *
     * @param  \Atomic\Http\Request  $request
     * @param  mixed  $response
     * @return mixed
     */
    protected function prepareResponse(Request $request, $response)
    {
        if ($response instanceof JsonSerializable || is_array($response) || is_object($response)) {
            header('Content-Type: application/json');
            return json_encode($response);
        }
        if ($response instanceof Renderable) {
            return $response->render();
        }
        return $response;
    }

    /**
     * Return the route URL
     *
     * @param \Atomic\Routing\Route $route
     * @return string
     */
    public function routeUrl(Route $route): string
    {
        return $this->container['url']->ajax($route->uri());
    }
}
