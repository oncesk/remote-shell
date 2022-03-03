<?php

namespace Shell\Console\Command\Finder;

use Shell\Console\Command\AliasAwareInterface;
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

        foreach ($store->getAll() as $command) {
            if ($command instanceof AliasAwareInterface && in_array($name, $command->getAliases())) {
                return $command;
            }
        }

		throw new CommandNotFoundException(sprintf(
			'Command \'%s\' not found',
			$name
		));
	}
}