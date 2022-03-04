<?php

namespace Shell\Console;

use Shell\Console\Command\BroadcastCommand;
use Shell\Console\Command\Date;
use Shell\Console\Command\EchoCommand;
use Shell\Console\Command\Finder\FinderInterface;
use Shell\Console\Command\HelpCommand;
use Shell\Console\Command\Quit;
use Shell\Console\Command\StoreInterface;
use Shell\Loop\LoopInterface;
use Shell\Server\ConnectionInterface;
use Shell\Server\ServerInterface;

class Application implements ApplicationInterface
{
    public function __construct(
        private ShellInterface $shell,
        private StoreInterface $store,
        private LoopInterface $loop,
        private FinderInterface $finder
    ) {
    }

    public function run(ServerInterface $server, ShellInterface $shell)
    {
        $this
            ->configureServer($server, $shell)
            ->configureCommands($this->store, $server)
        ;

        $server->start();
    }

    protected function handleConnection(ConnectionInterface $connection, ShellInterface $shell)
    {
        $shell->init($connection);

        $this->bindCommandListener($connection->read(), $connection, $shell);
    }

    protected function handleCloseConnection(ConnectionInterface $connection)
    {
    }

    protected function bindCommandListener(string $input, ConnectionInterface $connection, ShellInterface $shell)
    {
        echo 'Memory usage: ' . (memory_get_usage(true) / 1024) . PHP_EOL;
        $shell->execute($input, $connection);

        if ($connection->isOpen()) {
            $this->bindCommandListener($connection->read(), $connection, $shell);
        }
    }

    protected function configureServer(ServerInterface $server, ShellInterface $shell): Application
    {
        $server
            ->onConnection(fn ($c) => $this->handleConnection($c, $shell))
            ->onClose($this->handleCloseConnection(...))
        ;

        return $this;
    }

    protected function configureCommands(StoreInterface $store, ServerInterface $server): Application
    {
        $store
            ->add(new HelpCommand($store, $this->finder))
            ->add(new Date())
            ->add(new BroadcastCommand($server))
            ->add(new EchoCommand())
            ->add(new Quit())
        ;

        return $this;
    }
}
