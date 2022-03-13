<?php

namespace Shell\Console\Env;

interface EnvAwareInterface
{
    public function setEnvironment(EnvInterface $env): void;
}
