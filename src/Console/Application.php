<?php

namespace Shell\Console;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use Shell\Console\Command\BroadcastCommand;
use Shell\Console\Command\Date;
use Shell\Console\Command\EchoCommand;
use Shell\Console\Command\Env;
use Shell\Console\Command\Filesystem\Cat;
use Shell\Console\Command\Filesystem\Cd;
use Shell\Console\Command\Filesystem\Ls;
use Shell\Console\Command\Filesystem\Pwd;
use Shell\Console\Command\Finder\FinderInterface;
use Shell\Console\Command\HelpCommand;
use Shell\Console\Command\Quit;
use Shell\Console\Command\SetEnv;
use Shell\Console\Command\StoreInterface;
use Shell\Loop\LoopInterface;
use Shell\Server\ConnectionInterface;
use Shell\Server\ServerInterface;

class Application implements ApplicationInterface
{
    private vfsStreamDirectory $vfsStream;

    public function __construct(
        private ShellInterface $shell,
        private StoreInterface $store,
        private LoopInterface $loop,
        private FinderInterface $finder
    ) {
        $this->vfsStream = vfsStream::setup('root');

        $this->configureFilesystem($this->vfsStream);
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
        $url = $this->vfsStream->url();

        $store
            ->add(new HelpCommand($store, $this->finder))
            ->add(new Date())
            ->add(new BroadcastCommand($server))
            ->add(new EchoCommand())
            ->add(new Cd($url))
            ->add(new Ls($url))
            ->add(new Pwd($url))
            ->add(new Cat($url, $this->loop))
            ->add(new Quit())
            ->add(new Env())
            ->add(new SetEnv())
        ;

        return $this;
    }

    protected function configureFilesystem(vfsStreamDirectory $directory)
    {
        $home = new vfsStreamDirectory('home', 0755);
        $homeUser = new vfsStreamDirectory('tony', 0777);
        $readme = new vfsStreamFile('README');
        $readme->setContent('It is Remote Shell v0.1');
        $homeUser->addChild($readme);
        $home->addChild($homeUser);

        $bin = new vfsStreamDirectory('bin', 0777);
        $directory->addChild($home);
        $directory->addChild($bin);
    }
}
