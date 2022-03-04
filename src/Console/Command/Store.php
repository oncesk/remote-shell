<?php

namespace Shell\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Store implements StoreInterface
{
    /**
     * @var CommandInterface[]
     */
    private array $store = [];

    public function add(CommandInterface $command): StoreInterface
    {
        $this->store[$command->getName()] = $command;

        return $this;
    }

    public function get(string $name): CommandInterface
    {
        return $this->store[$name] ?? $this->getNotFoundCommand();
    }

    public function has(string $name): bool
    {
        return isset($this->store[$name]);
    }

    public function getAll(): array
    {
        return $this->store;
    }

    private function getNotFoundCommand(): CommandInterface
    {
        return new class () implements CommandInterface {
            public function getName(): string
            {
                return 'Not found';
            }

            public function execute(InputInterface $input, OutputInterface $output)
            {
                $output->write('Command not found');
            }
        };
    }
}
