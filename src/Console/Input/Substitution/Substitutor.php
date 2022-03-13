<?php

namespace Shell\Console\Input\Substitution;

use Shell\Console\Env\EnvInterface;
use Shell\Console\ShellInterface;

class Substitutor implements SubstitutorInterface
{
    /**
     * @var SubstitutorInterface[]
     */
    private array $substitutors;

    public function __construct(array $substitutors)
    {
        array_walk($substitutors, $this->add(...));
    }

    public function add(SubstitutorInterface $substitutor): static
    {
        $this->substitutors[$substitutor::class] = $substitutor;

        return $this;
    }

    public function substitute(string $input, EnvInterface $env, ShellInterface $shell): string
    {
        foreach ($this->substitutors as $substitutor) {
            $input = $substitutor->substitute($input, $env, $shell);
        }

        return $input;
    }
}
