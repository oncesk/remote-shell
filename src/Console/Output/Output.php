<?php

namespace Shell\Console\Output;

use Shell\Server\ConnectionInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;

class Output extends \Symfony\Component\Console\Output\Output implements WrittenInterface
{
    private int $written = 0;

    public function __construct(
        private ConnectionInterface $connection,
        int $verbosity = self::VERBOSITY_NORMAL,
        bool $decorated = true,
        OutputFormatterInterface $formatter = null
    ) {
        parent::__construct($verbosity, $decorated, $formatter);
    }

    public function getWrittenLength(): int
    {
        return $this->written;
    }

    protected function doWrite(string $message, bool $newline)
    {
        if ($newline) {
            $message .= \PHP_EOL;
        }

        $this->written += mb_strlen($message);
        $this->connection->write($message);
    }
}
