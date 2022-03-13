<?php

namespace Shell\Console\Command\Filesystem;

use Shell\Console\Command\UserLandCommandInterface;
use Shell\Console\Env\EnvAwareInterface;
use Shell\Console\Env\EnvAwareTrait;
use Shell\Console\Input\InputInterface;
use Shell\Loop\LoopInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class Cat implements UserLandCommandInterface, EnvAwareInterface
{
    use EnvAwareTrait;

    private InputDefinition $definition;

    public function __construct(private string $root, private LoopInterface $loop)
    {
        $this->definition = new InputDefinition([
            new InputArgument('file', InputArgument::REQUIRED, 'File to feed'),
        ]);
    }


    public function getName(): string
    {
        return 'cat';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $path = $this->env->get('DIR');

        $filename = $this->root . (str_starts_with($file, '/') ? $file : '/' . trim($path, '/') . '/' . $file);

        if (!file_exists($filename) || !is_file($filename)) {
            throw new FileNotFoundException("File $file is not found!");
        }

        $fp = fopen($filename, 'r');

        if (!$fp) {
            throw new \Exception('Unable to read file');
        }

        while (!feof($fp)) {
            $output->write(fgets($fp));
        }

        fclose($fp);
    }

    public function getDescription(): string
    {
        return 'Concatenate FILE(s) to standard output.';
    }

    public function getDefinition(): InputDefinition
    {
        return $this->definition;
    }
}
