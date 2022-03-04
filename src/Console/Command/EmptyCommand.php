<?php

namespace Shell\Console\Command;

use Shell\Console\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmptyCommand implements CommandInterface, MatchableCommandInterface
{
    public function getName(): string
    {
        return '_empty_';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write('');
    }

    public function isMatch(string $input): bool
    {
        return empty($input) || preg_match('/^\s+$/', $input);
    }
}
