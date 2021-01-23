<?php

namespace Atomic\Events;

use ReflectionFunction;
use ReflectionMethod;
use Atomic\Foundation\Application;

class Dispatcher
{
    /**
     * The application instance
     *
     * @var \Atomic\Foundation\Application
     */
    protected $app;

    /**
     * Create the event dispatcher
     *
     * @param \Atomic\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Register an event listener with the dispatcher.
     *
     * @param string|array $events
     * @param mixed $listener
     * @param int $priority
     * @return void
     */
    public function listen($events, $listener, int $priority = 10): void
    {
        $listener = $this->makeListener($listener);

        foreach ((array) $events as $event) {
            add_filter($event, $listener, $priority, $this->getParameterCount($listener));
        }
    }

    /**
     * Determine if a given event has listeners.
     *
     * @param string $eventName
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return (bool) has_filter($eventName);
    }

    /**
     * Register an event subscriber with the dispatcher.
     *
     * @param object|string $subscriber
     * @return void
     */
    public function subscribe($subscriber): void
    {
        $subscriber = $this->resolveSubscriber($subscriber);

        $subscriber->subscribe($this);
    }

    /**
     * Resolve the subscriber instance.
     *
     * @param object|string $subscriber
     * @return mixed
     */
    protected function resolveSubscriber($subscriber)
    {
        if (is_string($subscriber)) {
            return $this->app->make($subscriber);
        }

        return $subscriber;
    }

    /**
     * Fire an event and call the listeners.
     *
     * @param string|object $event
     * @param mixed|null $payload
     * @return mixed
     */
    public function dispatch($event, $payload = null)
    {
        if (is_object($event)) {
            [$payload, $event] = [$event, get_class($event)];
        }

        if (is_array($payload) && !empty($payload)) {
            return apply_filters($event, ...$payload);
        } else {
            return apply_filters($event, $payload);
        }
    }

    /**
     * Remove a set of listeners from the dispatcher.
     *
     * @param string $event
     * @return void
     */
    public function forget(string $event): void
    {
        remove_all_filters($event);
    }

    /**
     * Make a listener
     *
     * @param mixed $listener
     *
     * @return callable|array
     */
    protected function makeListener($listener)
    {
        if (is_string($listener) && class_exists($listener)) {
            return [$this->app->make($listener), 'handle'];
        }
        return $listener;
    }

    /**
     * Return the argument count.
     *
     * @param mixed $listener
     * @return int
     */
    protected function getParameterCount($listener): int
    {
        if (is_object($listener)) {
            $listener = [$listener, '__invoke'];
        }

        $reflect = is_array($listener)
            ? new ReflectionMethod($listener[0], $listener[1])
            : new ReflectionFunction($listener);

        return $reflect->getNumberOfParameters();
    }
}
