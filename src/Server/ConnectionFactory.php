<?php

namespace Shell\Server;

use Shell\Loop\LoopInterface;

class ConnectionFactory implements ConnectionFactoryInterface
{
    public function create($socket, ServerInterface $server, LoopInterface $loop): ConnectionInterface
    {
        return new Connection($socket, $server, $loop);
    }
}
