<?php

namespace Shell\Loop;

class EvLoop implements LoopInterface
{
	private \EvLoop $loop;

	/**
	 * @var \EvIo[]
	 */
	private $read = [];

	/**
	 * @var \EvIo[]
	 */
	private $write = [];

	/**
	 * @var \Closure[]
	 */
	private array $always = [];

	private \EvTimer $tickTimer;

	private bool $isRunning = false;

	public function __construct()
	{
		$this->loop = new \EvLoop(io_interval: 0.1, timeout_interval: 0.2);
		$this->tickTimer = $this->loop->timer(0., 0.1, function () {
			array_map(fn($cb) => $cb(), $this->always);
		});
	}

	public function run(): void
	{
		$this->isRunning = true;

		while ($this->isRunning) {

			$flags = \Ev::RUN_ONCE;

			$this->loop->run($flags);
		}
	}

	public function stop(): void
	{
		$this->isRunning = false;
		$this->tickTimer->stop();
	}

	public function read($resource, \Closure $closure): LoopInterface
	{
		$this->read[(int) $resource] = $this->loop->io($resource, \Ev::READ, function () use ($resource, $closure) {
			$closure($resource);
		});

		return $this;
	}

	public function write($resource, \Closure $closure): LoopInterface
	{
		$this->write[(int) $resource] = $this->loop->io($resource, \Ev::WRITE, function () use ($resource, $closure) {
			$closure($resource);
		});

		return $this;
	}

	public function removeRead($resource): LoopInterface
	{
		$id = (int) $resource;

		if (isset($this->read[$id])) {
			$this->read[$id]->stop();
			unset($this->read[$id]);
		}

		return $this;
	}

	public function removeWrite($resource): LoopInterface
	{
		$id = (int) $resource;

		if (isset($this->write[$id])) {
			$this->write[$id]->stop();
			unset($this->write[$id]);
		}

		return $this;
	}

	public function always(\Closure $closure): LoopInterface
	{
		$this->always[] = $closure;

		return $this;
	}

	public function tick(\Closure $closure): LoopInterface
	{
		$this->loop->timer(0.0, 0,  $closure);

		return $this;
	}
}
