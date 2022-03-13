<?php

namespace Shell\Console\Command;

use Shell\Console\Env\EnvAwareInterface;
use Shell\Console\Env\EnvAwareTrait;
use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetEnv implements CommandInterface, EnvAwareInterface, MatchableCommandInterface
{
    use EnvAwareTrait;

    public function getName(): string
    {
        return 'setenv';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        preg_match('/(.*[^\s=])=(.*)/', (string) $input, $m);

        $this->env->set($m[1], $m[2]);
    }

    public function isMatch(string $input): bool
    {
        return preg_match('/.*[^\s=]=.*/', $input);
    }
}
