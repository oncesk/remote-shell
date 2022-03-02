<?php

namespace Shell\Server;

interface ServerInterface
{
	public function start(): void;
	public function stop(): void;
	public function onConnection(\Closure $callback): ServerInterface;
	public function onClose(\Closure $callback): ServerInterface;
	public function getConnections(): \SplObjectStorage;
}
