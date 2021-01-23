<?php

namespace Atomic\Support;

use ArrayAccess;
use Atomic\Contracts\Support\Arrayable;

class Collection implements ArrayAccess, Arrayable
{
    /**
     * The collection items
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create the collection instance
     *
     * @param mixed $items
     */
    public function __construct($items = [])
    {
        $this->items = Arr::wrap($items);
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     * @return self
     */
    public function add($item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get all of the items in the collection.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Execute a callback over each item.
     *
     * @param  callable  $callback
     * @return self
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param  int  $depth
     * @return static
     */
    public function flatten($depth = INF)
    {
        return new static(Arr::flatten($this->items, $depth));
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback)
    {
        $items = array_map($callback, $this->items, $keys = array_keys($this->items));

        return new static(array_combine($keys, $items));
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->map(function ($value) {
            return $value instanceof Arrayable ? $value->toArray() : $value;
        })->all();
    }

    /**
     * Reset the keys on the underlying array.
     *
     * @return static
     */
    public function values()
    {
        return new static(Arr::values($this->items));
    }



    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }
}
