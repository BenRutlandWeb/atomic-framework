<?php

namespace Atomic\Http;

use ArrayAccess;
use Closure;
use JsonSerializable;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest implements ArrayAccess, JsonSerializable
{
    /**
     * The user resolver
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * The route resolver
     *
     * @var \Closure
     */
    protected $routeResolver;

    /**
     * The validato resolver
     *
     * @var \Closure
     */
    protected $validatorResolver;

    /**
     * Create a new HTTP request from server variables.
     *
     * @return static
     */
    public static function capture(): Request
    {
        return parent::createFromGlobals();
    }

    /**
     * Merge new input into the current request's input array.
     *
     * @param  array  $input
     * @return self
     */
    public function merge(array $input): self
    {
        $this->getInputSource()->add($input);

        return $this;
    }

    /**
     * Get the input source for the request.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    public function getInputSource(): ParameterBag
    {
        return in_array($this->getRealMethod(), ['GET', 'HEAD']) ? $this->query : $this->request;
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return $this->getMethod();
    }

    /**
     * Get the root URL for the application.
     *
     * @return string
     */
    public function root(): string
    {
        return rtrim($this->getSchemeAndHttpHost() . $this->getBaseUrl(), '/');
    }

    /**
     * Get the URL (no query string) for the request.
     *
     * @return string
     */
    public function url(): string
    {
        return strtok($this->getUri(), '?');
    }

    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl(): string
    {
        return $this->getUri();
    }

    /**
     * Retrieve a server variable from the request.
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array|null
     */
    public function server(?string $key = null, $default = null)
    {
        return $this->retrieveItem('server', $key, $default);
    }

    /**
     * Determine if a header is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasHeader(string $key): bool
    {
        return (bool) $this->header($key);
    }

    /**
     * Retrieve a header from the request.
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array|null
     */
    public function header(?string $key = null, $default = null)
    {
        return $this->retrieveItem('headers', $key, $default);
    }

    /**
     * Retrieve a parameter item from a given source.
     *
     * @param  string  $source
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array|null
     */
    protected function retrieveItem(string $source, ?string $key, $default)
    {
        if (is_null($key)) {
            return $this->$source->all();
        }

        return $this->$source->get($key, $default);
    }

    /**
     * Return the request parameters
     *
     * @return array
     */
    public function all(): array
    {
        return $this->getInputSource()->all() + $this->query->all();
    }

    /**
     * Return the request inputs
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return (bool) $this->get($offset);
    }

    /**
     * Get the value at the given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->getInputSource()->set($offset, $value);
    }

    /**
     * Remove the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->getInputSource()->remove($offset);
    }

    /**
     * Get the user making the request.
     *
     * @return mixed
     */
    public function user()
    {
        return call_user_func($this->getUserResolver());
    }

    /**
     * Get the user resolver
     *
     * @return \Closure
     */
    public function getUserResolver(): Closure
    {
        return $this->userResolver ?: function () {
            //
        };
    }

    /**
     * Set the user resolver callback.
     *
     * @param  \Closure  $callback
     * @return self
     */
    public function setUserResolver(Closure $callback): self
    {
        $this->userResolver = $callback;

        return $this;
    }

    /**
     * Get the route handling the request.
     *
     * @return mixed
     */
    public function route()
    {
        return call_user_func($this->getRouteResolver());
    }

    /**
     * Get the route resolver callback.
     *
     * @return \Closure
     */
    public function getRouteResolver(): Closure
    {
        return $this->routeResolver ?: function () {
            //
        };
    }

    /**
     * Set the route resolver callback.
     *
     * @param  \Closure  $callback
     * @return self
     */
    public function setRouteResolver(Closure $callback): self
    {
        $this->routeResolver = $callback;

        return $this;
    }

    /**
     * Validate the request inputs by the rules passed
     *
     * @param array $rules
     * @param array $messages
     * @return array
     *
     * @throws \Atomic\Validation\ValidationException
     */
    public function validate(array $rules, array $messages = [])
    {
        $validator = call_user_func($this->getValidatorResolver());

        return $validator->validate($this, $rules, $messages);
    }

    /**
     * Get the validator resolver callback.
     *
     * @return \Closure
     */
    public function getValidatorResolver(): Closure
    {
        return $this->validatorResolver ?: function () {
            //
        };
    }

    /**
     * Set the validator resolver callback.
     *
     * @param  \Closure  $callback
     * @return self
     */
    public function setValidatorResolver(Closure $callback): self
    {
        $this->validatorResolver = $callback;

        return $this;
    }

    /**
     * Create a new request instance from the given request.
     *
     * @param  \Atomic\Http\Request  $from
     * @param  \Atomic\Http\Request|null  $to
     * @return static
     */
    public static function createFrom(self $from, $to = null)
    {
        $request = $to ?: new static;

        $files = $from->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $request->initialize(
            $from->query->all(),
            $from->request->all(),
            $from->attributes->all(),
            $from->cookies->all(),
            $files,
            $from->server->all(),
            $from->getContent()
        );

        $request->headers->replace($from->headers->all());

        #$request->setJson($from->json());

        $request->setUserResolver($from->getUserResolver());
        $request->setRouteResolver($from->getRouteResolver());
        $request->setValidatorResolver($from->getValidatorResolver());

        return $request;
    }

    /**
     * Check if an input element is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return (bool) $this->get($key);
    }

    /**
     * Get an input fro mthe request
     *
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }
}
