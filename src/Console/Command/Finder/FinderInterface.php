<?php

namespace Shell\Console\Command\Finder;

use Shell\Console\Command\CommandInterface;
use Shell\Console\Command\StoreInterface;
use Shell\Console\Input\InputInterface;

interface FinderInterface
{
    /**
     * @param InputInterface $input
     * @param StoreInterface $store
     *
     * @throws CommandNotFoundException
     *
     * @return CommandInterface
     */
    public function find(InputInterface $input, StoreInterface $store): CommandInterface;
}
