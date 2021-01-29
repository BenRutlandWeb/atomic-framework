<?php

namespace Atomic\Foundation\Http\Middleware;

use Closure;
use Atomic\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

class TransformsRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Atomic\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->clean($request);

        return $next($request);
    }

    /**
     * Clean the request's data.
     *
     * @param  \Atomic\Http\Request  $request
     * @return void
     */
    protected function clean(Request $request): void
    {
        $this->cleanParameterBag($request->query);

        if ($request->request !== $request->query) {
            $this->cleanParameterBag($request->request);
        }
    }

    /**
     * Clean the data in the parameter bag.
     *
     * @param  \Symfony\Component\HttpFoundation\ParameterBag  $bag
     * @return void
     */
    protected function cleanParameterBag(ParameterBag $bag): void
    {
        $bag->replace($this->cleanArray($bag->all()));
    }

    /**
     * Clean the data in the given array.
     *
     * @param  array  $data
     * @param  string  $keyPrefix
     * @return array
     */
    protected function cleanArray(array $data, string $keyPrefix = ''): array
    {
        return collect($data)->map(function ($value, $key) use ($keyPrefix) {
            return $this->cleanValue($keyPrefix . $key, $value);
        })->all();
    }

    /**
     * Clean the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function cleanValue(string $key, $value)
    {
        if (is_array($value)) {
            return $this->cleanArray($value, $key . '.');
        }

        return $this->transform($key, $value);
    }

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function transform(string $key, $value)
    {
        return $value;
    }
}
