<?php

namespace Shell\Console;

use Shell\Server\ConnectionInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class Output extends \Symfony\Component\Console\Output\Output
{
    public function __construct(
        private ConnectionInterface $connection,
        int $verbosity = self::VERBOSITY_NORMAL,
        bool $decorated = true,
        OutputFormatterInterface $formatter = null
    ) {
        parent::__construct($verbosity, $decorated, $formatter);
    }

    protected function doWrite(string $message, bool $newline)
    {
        if ($newline) {
            $message .= \PHP_EOL;
        }

        $this->connection->write($message);
    }
}
