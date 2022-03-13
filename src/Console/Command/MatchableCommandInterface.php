<?php

namespace Shell\Console\Command;

interface MatchableCommandInterface
{
    public function isMatch(string $input): bool;
}
