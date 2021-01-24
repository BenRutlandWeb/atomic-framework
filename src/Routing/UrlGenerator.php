<?php

namespace Atomic\Routing;

use Atomic\Http\Request;
use Atomic\Routing\Exceptions\RouteNotFoundException;

class UrlGenerator
{
    /**
     * The routes
     *
     * @var \Atomic\Routing\RouteCollection
     */
    protected $routes;

    /**
     * The ajax routes
     *
     * @var \Atomic\Routing\RouteCollection
     */
    protected $ajaxRoutes;

    /**
     * The request
     *
     * @var \Atomic\Http\Request
     */
    protected $request;

    /**
     * The asset root
     *
     * @var string
     */
    protected $assetRoot;


    /**
     * Create a URL generator instance
     *
     * @param \Atomic\Routing\RouteCollection $routes
     * @param \Atomic\Routing\RouteCollection $ajaxRoutes
     * @param \Atomic\Http\Request $request
     * @param string|null $assetRoot
     */
    public function __construct(
        RouteCollection $routes,
        RouteCollection $ajaxRoutes,
        Request $request,
        ?string $assetRoot = null
    ) {
        $this->routes = $routes;
        $this->ajaxRoutes = $ajaxRoutes;
        $this->request = $request;
        $this->assetRoot = $assetRoot;
    }

    /**
     * Get the current URL without query parameters.
     *
     * @return string
     */
    public function current(): string
    {
        return $this->request->url();
    }

    /**
     * Get the current URL including query parameters.
     *
     * @return string
     */
    public function full(): string
    {
        return $this->request->fullUrl();
    }

    /**
     * Get the URL for the previous request.
     *
     * @param string|null $fallback
     * @return string
     */
    public function previous(?string $fallback = null): string
    {
        if ($previous = wp_get_referer()) {
            return $previous;
        }
        return $fallback ?? $this->home();
    }

    /**
     * Get the URL to a named route.
     *
     * @param string $name
     * @param array $parameters
     * @param boolean $absolute
     * @return string
     *
     * @throws \Atomic\Routing\Exception\RouteNotFoundException
     */
    public function route(string $name, array $parameters = [], bool $absolute = true): string
    {
        if (!is_null($route = $this->routes->getByName($name))) {
            return $this->toRoute($route, $parameters, $absolute);
        }

        throw new RouteNotFoundException("Route [{$name}] not defined.");
    }

    /**
     * Get the URL to a named ajax route.
     *
     * @param string $name
     * @param array $parameters
     * @param boolean $absolute
     * @return string
     *
     * @throws \Atomic\Routing\Exception\RouteNotFoundException
     */
    public function ajaxRoute(string $name, array $parameters = [], bool $absolute = true): string
    {
        if (!is_null($route = $this->ajaxRoutes->getByName($name))) {
            return $this->toRoute($route, $parameters, $absolute);
        }

        throw new RouteNotFoundException("Route [{$name}] not defined.");
    }

    /**
     * Get the URL for a given route instance.
     *
     * @param \Atomic\Routing\Route $route
     * @param array $parameters
     * @param boolean $absolute
     * @return string
     */
    public function toRoute(Route $route, array &$parameters, bool $absolute = true): string
    {
        [$url, $parameters] = $this->mergeParametersWithUrl(
            $route->getUrl(),
            $parameters
        );

        $url = add_query_arg($parameters, $url);

        return $absolute ? $url : wp_make_link_relative($url);
    }

    /**
     * Merge the parameters with the URL and remove any superflous "action"
     * attributes from the parameter list.
     *
     * @param string $url
     * @param array $parameters
     * @return array
     */
    public function mergeParametersWithUrl(string $url, array $parameters): array
    {
        // REST routes may have dynamic properties set in which case we can swap
        // them out with the parameters passed though the URL generator. The
        // parameter is then unset from the parameter list so it doesn't get
        // appended as a query string.
        $url = preg_replace_callback('@\{([\w]+?)\??\}@', function ($matches) use (&$parameters) {
            $match = $parameters[$matches[1]] ?? $matches[0];
            unset($parameters[$matches[1]]);
            return $match;
        }, $url);

        // if the route already contains an action query parameter, remove any
        // action from the parameter array. This will stop ajax routes having
        // their action overridden.
        if (strpos($url, '?action=') !== false) {
            unset($parameters['action']);
        }

        return [$url, $parameters];
    }

    /**
     * Generate the URL to an application asset.
     *
     * @param  string  $path
     * @return string
     */
    public function asset(string $path): string
    {
        return $this->theme(trim($this->assetRoot, '/') . '/' . trim($path, '/'));
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     * @return bool
     */
    public function isValidUrl(string $path): bool
    {
        if (!preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }

    /**
     * return the registration URL
     *
     * @return string
     */
    public function register(string $redirect = ''): string
    {
        return add_query_arg('redirect_to', urlencode($redirect), wp_registration_url());
    }

    /**
     * return the login URL
     *
     * @param string $redirect
     * @return string
     */
    public function login(string $redirect = ''): string
    {
        return wp_login_url($redirect);
    }

    /**
     * Return the logout URL
     *
     * @param string $redirect
     * @return string
     */
    public function logout(string $redirect = ''): string
    {
        return wp_logout_url($redirect);
    }

    /**
     * Return the home URL
     *
     * @param string $path
     * @return string
     */
    public function home(string $path = ''): string
    {
        return home_url($path);
    }

    /**
     * Return the admin URL
     *
     * @param string $path
     * @return string
     */
    public function admin(string $path = ''): string
    {
        return admin_url($path);
    }

    /**
     * Return the ajax URL
     *
     * @param string $action
     * @return string
     */
    public function ajax(string $action = ''): string
    {
        return $this->admin('admin-ajax.php' . ($action ? "?action={$action}" : ''));
    }

    /**
     * Return the REST URL
     *
     * @param string $path
     * @return string
     */
    public function rest(string $path = ''): string
    {
        return rest_url($path);
    }

    /**
     * Redirect to another page, with an optional status code
     *
     * @param string  $url
     * @param integer $status
     * @return void
     */
    public function redirect(string $url, int $status = 302): void
    {
        die(wp_redirect($url, $status));
    }

    /**
     * Return the theme root URL
     *
     * @param string $path
     * @return string
     */
    public function theme(string $path = ''): string
    {
        return get_template_directory_uri() . ($path ? '/' . trim($path, '/') : $path);
    }
}
