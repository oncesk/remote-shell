<?php

namespace Shell\Server;

use Shell\Loop\LoopInterface;

class Server implements ServerInterface
{
    private \SplObjectStorage $connections;

    /**
     * @var \Closure[]
     */
    private array $onConnection = [];
    private array $onClose = [];

    public function __construct(
        private LoopInterface $loop,
        private $socket,
        private ConnectionFactoryInterface $connectionFactory
    ) {
        $this->connections = new \SplObjectStorage();
    }

    public function start(): void
    {
        $this->loop->always($this->accept(...));

        $this->loop->run();
    }

    public function stop(): void
    {
        array_map(fn (ConnectionInterface $connection) => $connection->close(), $this->connections);
        $this->loop->stop();
    }

    public function onConnection(\Closure $callback): ServerInterface
    {
        $this->onConnection[] = $callback;

        return $this;
    }

    public function onClose(\Closure $callback): ServerInterface
    {
        return $this;
    }

    public function getConnections(): \SplObjectStorage
    {
        return $this->connections;
    }

    private function accept(): void
    {
        if ($sock = @stream_socket_accept($this->socket, 0)) {
            stream_set_blocking($sock, 0);

            $connection = $this->connectionFactory->create($sock, $this, $this->loop);
            $connection->on('close', $this->handleClose(...));

            $this->connections->attach($connection);

            array_walk($this->onConnection, fn($callback) => $this->callCallback($connection, $callback));
        }
    }

    private function callCallback(ConnectionInterface $connection, \Closure $callback): void
    {
        $fiber = new \Fiber(function (ConnectionInterface $connection, \Closure $closure) {
            $closure($connection);
        });

        $fiber->start($connection, $callback);
    }

    private function handleClose(ConnectionInterface $connection, $socket): void
    {
        $this->loop
            ->removeRead($socket)
            ->removeWrite($socket);

        $this->connections->detach($connection);
    }
}
