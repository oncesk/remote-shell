<?php

namespace Shell\Loop;

class SelectLoop implements LoopInterface
{
	private array $read = [];
	private array $readCallback = [];
	private array $write = [];
	private array $writeCallback = [];

	private \SplQueue $tickQueue;

	/**
	 * @var \Closure[]
	 */
	private array $always = [];

	private bool $isRunning = false;

	public function __construct(private int $timeout = 2000000)
	{
		$this->tickQueue = new \SplQueue();
	}

	public function run(): void
	{
		if ($this->isRunning) {
			return;
		}
		$this->isRunning = true;

		while($this->isRunning) {

			if ($count = $this->tickQueue->count()) {
				while ($count--) {
					($this->tickQueue->dequeue())($this);
				}
			}

			if ($this->always) {
				array_map('call_user_func', $this->always);
			}

			[$read, $write] = $this->select();

			if (!$write && !$read) {
				usleep(2000000);
				continue;
			}

			if ($read) {
				array_map(fn($resource) => $this->readCallback[(int) $resource]($resource), $read);
			}

			if ($write) {
				array_map(fn($resource) => $this->writeCallback[(int) $resource]($resource), $write);
			}
		}
	}

	public function stop(): void
	{
		$this->isRunning = false;
	}

	public function read($resource, \Closure $closure): LoopInterface
	{
		$id = (int) $resource;

		$this->read[$id] = $resource;
		$this->readCallback[$id] = $closure;
		return $this;
	}

	public function write($resource, \Closure $closure): LoopInterface
	{
		$id = (int) $resource;

		$this->write[$id] = $resource;
		$this->writeCallback[$id] = $closure;

		return $this;
	}

	public function always(\Closure $closure): LoopInterface
	{
		$this->always[] = $closure;

		return $this;
	}

	public function tick(\Closure $closure): LoopInterface
	{
		$this->tickQueue->enqueue($closure);

		return $this;
	}

	public function removeRead($resource): LoopInterface
	{
		$id = (int) $resource;

		if (isset($this->read[$id])) {
			unset($this->read[$id], $this->readCallback[$id]);
		}

		return $this;
	}

	public function removeWrite($resource): LoopInterface
	{
		$id = (int) $resource;

		if (isset($this->write[$id])) {
			unset($this->write[$id], $this->writeCallback[$id]);
		}

		return $this;
	}


	private function select(): array
	{
		$read = $this->read;
		$write = $this->write;
		$except = null;

		if (!$read && !$write) {
			return [null, null];
		}

		if (@stream_select($read, $write, $except, 0, $this->timeout)) {
			return [$read, $write];
		}

		return [null, null];
	}
}