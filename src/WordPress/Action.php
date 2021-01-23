<?php

namespace Atomic\WordPress;

use Atomic\Events\Dispatcher;

class Action
{
    /**
     * The events dispatcher
     *
     * @var \Atomic\Events\Dispatcher
     */
    protected $events;

    /**
     * Create the action instance
     *
     * @param \Atomic\Events\Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Add an action
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
     * Do an action
     *
     * @param string $filter
     * @param mixed ...$params
     * @return void
     */
    public function do(string $filter, ...$params): void
    {
        $this->events->dispatch($filter, $params);
    }

    /**
     * Remove an action
     *
     * @param string $filter
     * @return void
     */
    public function remove(string $filter): void
    {
        $this->events->forget($filter);
    }
}
