<?php

namespace Shell\Console\Command\Finder;

use Shell\Console\Command\CommandInterface;
use Shell\Console\Command\StoreInterface;
use Shell\Console\Input\InputInterface;

class Finder implements FinderInterface
{
	/**
	 * @var FinderInterface[]
	 */
	private array $finders;

	public function __construct(FinderInterface ...$finders)
	{
		$this->finders = $finders;
	}

	public function find(InputInterface $input, StoreInterface $store): CommandInterface
	{
		foreach ($this->finders as $finder) {
			try {
				return $finder->find($input, $store);
			} catch (CommandNotFoundException $exception) {
				echo $exception->getMessage() . PHP_EOL;
			}
		}

		throw new CommandNotFoundException('Command is not found');
	}
}
