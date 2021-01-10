<?php declare(strict_types=1);

namespace SeStep\NetteFeed;

/**
 * FeedEvents serves as a control bus, allowing events to be passed from within feed
 * controls. These events are passed to registered subscribers through $type-$event.
 *
 * The parameter $type serves as a group of events and $event as a specific event
 * from a group. Together they form something like an eventFqn (or an event fully
 * qualified name).
 */
final class FeedEvents
{
    private array $events = [];

    public function register(string $type, string $event, $callback)
    {
        if (!isset($this->events[$type])) {
            $this->events[$type] = [];
        }

        if (!isset($this->events[$type][$event])) {
            $this->events[$type][$event] = [];
        }

        $this->events[$type][$event][] = $callback;
    }

    public function fire(string $type, string $event, ...$arguments)
    {
        $callbacks = $this->events[$type][$event] ?? [];

        foreach ($callbacks as $callback) {
            call_user_func_array($callback, $arguments);
        }
    }
}
