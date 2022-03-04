<?php

namespace Shell\Console\Command;

use Shell\Console\Input\InputInterface;

interface MatchableCommandInterface
{
    public function isMatch(InputInterface $input): bool;
}
