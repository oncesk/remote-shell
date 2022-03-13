<?php

namespace Shell\Console\Command\Filesystem;

use Shell\Console\Command\UserLandCommandInterface;
use Shell\Console\Env\EnvAwareInterface;
use Shell\Console\Env\EnvAwareTrait;
use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class Cd implements UserLandCommandInterface, EnvAwareInterface
{
    use EnvAwareTrait;

    private InputDefinition $definition;

    public function __construct(private string $root)
    {
        $this->definition = new InputDefinition([
            new InputArgument('path', InputArgument::OPTIONAL, 'Path to change'),
        ]);
    }

    public function getName(): string
    {
        return 'cd';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path') ?? '/';
        $currentDir = $this->env->get('DIR');

        $checkoutDir = str_starts_with($path, '/') ? $path : rtrim($currentDir, '/') . '/' . $path;

        if ($this->isDir($checkoutDir)) {
            $this->env->set('DIR', $checkoutDir);
        }
    }

    public function getDescription(): string
    {
        return 'Change the shell working directory';
    }

    public function getDefinition(): InputDefinition
    {
        return $this->definition;
    }

    private function isDir(string $path): bool
    {
        return file_exists(str_starts_with($path, '/') ? $this->root . $path : $this->root . '/' . $path);
    }
}
