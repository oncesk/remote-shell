<?php

namespace Shell\Console\Command\Finder;

use Shell\Console\Command\CommandInterface;
use Shell\Console\Command\StoreInterface;
use Shell\Console\Input\InputInterface;

class ByName implements FinderInterface
{
	public function find(InputInterface $input, StoreInterface $store): CommandInterface
	{
		$name = $input->getName();

		if ($store->has($name)) {
			return $store->get($name);
		}

		throw new CommandNotFoundException(sprintf(
			'Command \'%s\' not found',
			$name
		));
	}
}