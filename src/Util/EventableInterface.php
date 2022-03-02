<?php

namespace Shell\Util;

interface EventableInterface
{
	/**
	 * @param string $event
	 * @param \Closure $callback
	 *
	 * @return $this
	 */
	public function on(string $event, \Closure $callback);
	public function emit(string $event, array $arguments): void;
}