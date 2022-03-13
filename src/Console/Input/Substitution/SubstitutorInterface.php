<?php

namespace Shell\Console\Input\Substitution;

use Shell\Console\Env\EnvInterface;
use Shell\Console\ShellInterface;

interface SubstitutorInterface
{
    public function substitute(string $input, EnvInterface $env, ShellInterface $shell): string;
}
