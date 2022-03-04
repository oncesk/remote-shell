<?php

namespace Shell\Console\Command\Finder;

use Shell\Console\Command\CommandInterface;
use Shell\Console\Command\MatchableCommandInterface;
use Shell\Console\Command\StoreInterface;
use Shell\Console\Input\InputInterface;

class MatchableFinder implements FinderInterface
{
    public function find(InputInterface $input, StoreInterface $store): CommandInterface
    {
        $inputString = (string) $input;

        foreach ($store->getAll() as $command) {
            if ($command instanceof MatchableCommandInterface) {
                if ($command->isMatch($inputString)) {
                    return $command;
                }
            }
        }

        throw new CommandNotFoundException('Command is not found');
    }
}
