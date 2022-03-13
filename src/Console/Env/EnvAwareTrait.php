<?php

namespace Shell\Console\Env;

trait EnvAwareTrait
{
    protected EnvInterface $env;

    public function setEnvironment(EnvInterface $env): void
    {
        $this->env = $env;
    }
}
