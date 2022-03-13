<?php

namespace Shell\Console\Input;

use Shell\Loop\LoopInterface;

class Stdin implements StdinInterface
{
    public function __construct(private $stream, private LoopInterface $loop)
    {}

    public function read()
    {
        // TODO: Implement read() method.
    }

    public function __destruct()
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }
}
