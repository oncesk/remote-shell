<?php

namespace Shell\Console\Input;

class Stdin implements StdinInterface
{
    public function __construct(private $stream)
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
