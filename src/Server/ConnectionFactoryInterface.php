<?php

namespace Shell\Server;

use Shell\Loop\LoopInterface;

interface ConnectionFactoryInterface
{
    public function create($socket, ServerInterface $server, LoopInterface $loop): ConnectionInterface;
}
