<?php

namespace Shell\Console\Input\Substitution;

use Shell\Console\Env\EnvInterface;
use Shell\Console\ShellInterface;

class NestedCommandSubstitutor implements SubstitutorInterface
{
    public function substitute(string $input, EnvInterface $env, ShellInterface $shell): string
    {
        if (preg_match_all('/\$\((.[^\)]+)\)/', $input, $m)) {
            //  todo implement nested execution
            throw new \RuntimeException('Nested callas are not supported at the moment.');
        }

        return $input;
    }
}
