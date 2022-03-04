<?php

namespace Shell\Console;

use Shell\Server\ConnectionInterface;

interface ShellInterface
{
    public function init(ConnectionInterface $connection);
    public function execute(string $input, ConnectionInterface $connection);
}
