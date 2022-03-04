<?php

namespace Shell\Console\Command;

use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CommandInterface
{
    public function getName(): string;
    public function execute(InputInterface $input, OutputInterface $output);
}
