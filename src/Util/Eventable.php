<?php

namespace Shell\Util;

trait Eventable
{
    private array $events = [];

    /**
     * @param string $event
     * @param \Closure $callback
     *
     * @return $this
     */
    public function on(string $event, \Closure $callback)
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        $this->events[$event][] = $callback;

        return $this;
    }

    public function emit(string $event, array $arguments): void
    {
        if (isset($this->events[$event])) {
            array_map(fn (\Closure $callback) => $callback(...$arguments), $this->events[$event]);
        }
    }
}
