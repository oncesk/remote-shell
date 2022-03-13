<?php

namespace Shell\Console\Input\Substitution;

use Shell\Console\Env\EnvInterface;
use Shell\Console\ShellInterface;

class VarSubstitutor implements SubstitutorInterface
{
    public function substitute(string $input, EnvInterface $env, ShellInterface $shell): string
    {
        return strtr($input, $this->makeSubstitutionMap($env));
    }

    private function makeSubstitutionMap(EnvInterface $env): array
    {
        $map = [];

        foreach ($env->toArray() as $variable => $value) {
            $map['$' . $variable] = $value;
            $map['${' . $variable . '}'] = $value;
        }

        return $map;
    }
}
