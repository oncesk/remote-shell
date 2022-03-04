<?php

namespace Shell\Server;

use Shell\Loop\LoopInterface;

class CallableConnectionFactory implements ConnectionFactoryInterface
{
    public function __construct(private \Closure $creator)
    {
    }

    public function create($socket, ServerInterface $server, LoopInterface $loop): ConnectionInterface
    {
        return call_user_func($this->creator, $socket, $server, $loop);
    }
}
