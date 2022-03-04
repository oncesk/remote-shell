<?php

namespace Shell\Server;

use Psr\Log\LoggerInterface;

class LoggedServer implements ServerInterface
{
    public function __construct(private LoggerInterface $logger, private ServerInterface $server)
    {
    }

    public function start(): void
    {
        $this->log('Starting...');
        $this->server->start();
        $this->log('Started');
    }

    public function stop(): void
    {
        $this->log('Stopping...');
        $this->server->stop();
    }

    public function onConnection(\Closure $callback): ServerInterface
    {
        $this->log('onConnection called');
        $this->server->onConnection($callback);

        return $this;
    }

    public function onClose(\Closure $callback): ServerInterface
    {
        $this->log('onClose called');
        $this->server->onClose($callback);

        return $this;
    }

    public function getConnections(): \SplObjectStorage
    {
        return $this->server->getConnections();
    }

    private function log(string $message)
    {
        $this->logger->debug('Server : ' . $message);
    }
}
