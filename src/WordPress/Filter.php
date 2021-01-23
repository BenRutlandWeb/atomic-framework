<?php

namespace Atomic\WordPress;

use Atomic\Events\Dispatcher;

class Filter
{
    /**
     * The events dispatcher
     *
     * @var \Atomic\Events\Dispatcher
     */
    protected $events;

    /**
     * Create the filter instance
     *
     * @param \Atomic\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Add a filter
     *
     * @param string $filter
     * @param mixed $action
     * @return void
     */
    public function add(string $filter, $action): void
    {
        $this->events->listen($filter, $action);
    }

    /**
     * Apply a filter
     *
     * @param string $filter
     * @param mixed ...$params
     * @return mixed
     */
    public function apply(string $filter, ...$params)
    {
        return $this->events->dispatch($filter, $params);
    }

    /**
     * Remove a filter
     *
     * @param string $filter
     * @return void
     */
    public function remove(string $filter): void
    {
        $this->events->forget($filter);
    }
}
