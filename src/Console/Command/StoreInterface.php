<?php

namespace Shell\Console\Command;

interface StoreInterface
{
	public function add(CommandInterface $command): StoreInterface;
	public function get(string $name): CommandInterface;
	public function has(string $name): bool;

	/**
	 * @return CommandInterface[]
	 */
	public function getAll(): array;
}