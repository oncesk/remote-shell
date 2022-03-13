<?php

namespace Shell\Console\Command\Filesystem;

use Shell\Console\Command\CommandInterface;
use Shell\Console\Env\EnvAwareInterface;
use Shell\Console\Env\EnvAwareTrait;
use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Pwd implements CommandInterface, EnvAwareInterface
{
    use EnvAwareTrait;

    public function getName(): string
    {
        return 'pwd';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write($this->env->get('DIR'));
    }
}
