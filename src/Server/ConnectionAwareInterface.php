<?php

namespace Shell\Server;

interface ConnectionAwareInterface
{
    public function getConnection(): ?ConnectionInterface;
    public function setConnection(ConnectionInterface $connection): void;
}
