<?php declare(strict_types=1);

namespace PAF\Common\Feed;

final class FeedEvents
{
    private $events = [];

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
