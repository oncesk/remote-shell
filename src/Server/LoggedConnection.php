<?php

namespace Shell\Server;

use Psr\Log\LoggerInterface;

class LoggedConnection implements ConnectionInterface
{
    public function __construct(private LoggerInterface $logger, private ConnectionInterface $connection)
    {
    }

    public function isOpen(): bool
    {
        return $this->connection->isOpen();
    }

    public function read(): ?string
    {
        $id = (string) $this->getStream();

        $this->logger->debug("Connection [$id] : Reading");
        $read = $this->connection->read();

        $this->logger->debug("Connection [$id] : Read completed");

        return $read;
    }

    public function write($data): void
    {
        $id = (string) $this->getStream();

        $this->logger->debug("Connection [$id] : Writing");
        $this->connection->write($data);
    }

    public function close()
    {
        $id = (string) $this->getStream();
        $this->logger->debug("Connection [$id] : Closing...");
        $this->connection->close();
        $this->logger->debug("Connection [$id] : Closed");
    }

    public function getStream()
    {
        return $this->connection->getStream();
    }

    public function __call(string $name, array $arguments)
    {
        return $this->connection->$name(...$arguments);
    }
}
