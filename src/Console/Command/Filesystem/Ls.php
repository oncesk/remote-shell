<?php

namespace Shell\Console\Command\Filesystem;

use Shell\Console\Command\AliasAwareInterface;
use Shell\Console\Command\UserLandCommandInterface;
use Shell\Console\Env\EnvAwareInterface;
use Shell\Console\Env\EnvAwareTrait;
use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Ls implements UserLandCommandInterface, AliasAwareInterface, EnvAwareInterface
{
    use EnvAwareTrait;

    private InputDefinition $definition;

    public function __construct(private string $root)
    {
        $this->definition = new InputDefinition([
            new InputOption(
                'long',
                'l',
                InputOption::VALUE_NONE,
                'use a long listing format'
            ),
        ]);
    }

    public function getName(): string
    {
        return 'ls';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $this->env->get('DIR');
        $iterator = new \DirectoryIterator($this->root . $dir);

        $content = [];

        foreach ($iterator as $item) {
            if ('/' === $dir && $item->isDot()) {
                continue;
            }
            $content[] = implode('', $this->handleItem($input, $item));
        }

        $output->write(implode(
            "\n",
            $content
        ));
    }

    public function getDescription(): string
    {
        return <<<DESC
List information about the FILEs (the current directory by default).
Sort entries alphabetically if none of -cftuvSUX nor --sort is specified.
DESC;

    }

    public function getDefinition(): InputDefinition
    {
        return $this->definition;
    }

    public function getAliases(): array
    {
        return [
            'll',
        ];
    }

    private function handleItem(InputInterface $input, \SplFileInfo $item): array
    {
        $info = [];

        if ($input->getOption('long')) {
            $info = array_merge($info, [
                ' ',
                $item->isDir() ? 'd' : '-',
                $item->isReadable() ? 'r' : '-',
                $item->isWritable() ? 'w' : '-',
                !$item->isDir() && $item->isExecutable() ? 'x' : '-',
            ]);
        }

        return array_merge($info, [
            ' ',
            $item->getFilename(),
        ]);
    }
}
