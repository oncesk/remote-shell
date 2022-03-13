<?php

namespace Shell\Console\Command;

use Shell\Console\Env\EnvAwareInterface;
use Shell\Console\Env\EnvAwareTrait;
use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Env implements CommandInterface, EnvAwareInterface
{
    use EnvAwareTrait;

    public function getName(): string
    {
        return 'env';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->write($this->env);
    }
}
