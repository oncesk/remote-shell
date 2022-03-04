<?php

namespace Shell\Console\Input;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface as SymfonyInput;

interface InputInterface extends SymfonyInput
{
    public function getName(): string;
    public function setDefinition(InputDefinition $definition): void;
}
