<?php

namespace Shell\Server;

interface ConnectionInterface
{
    public function isOpen(): bool;
    public function read(): ?string;
    public function write($data): void;
    public function close();
    public function getStream();
}
