<?php

namespace Shell\Server;

use Shell\Loop\LoopInterface;
use Shell\Util\Eventable;
use Shell\Util\EventableInterface;

class Connection implements ConnectionInterface, EventableInterface
{
    use Eventable;

    private bool $open = true;

    public function __construct(private $socket, private ServerInterface $server, private LoopInterface $loop)
    {
    }

    public function isOpen(): bool
    {
        return $this->open;
    }

    public function read(): ?string
    {
        if (!$this->isOpen()) {
            return null;
        }

        $fiber = \Fiber::getCurrent();

        $this->loop->read($this->socket, function ($socket) use ($fiber) {
            if ($fiber->isSuspended()) {
                $read = fgets($socket);

                if (false === $read) {
                    $this->validateSocket($socket, $fiber);
                }

                $fiber->resume($read);
            }
        });
        $data = \Fiber::suspend();
        return $data ? trim($data) : $data;
    }

    public function write($data): void
    {
        if (!$this->isOpen()) {
            return;
        }

        $fiber = \Fiber::getCurrent();

        $this->loop->write($this->socket, function ($socket) use ($data, $fiber) {
            if (false === fwrite($socket, $data)) {
                $this->validateSocket($socket, $fiber);
            }
            $this->loop->removeWrite($socket);
            $fiber->resume();
        });
        \Fiber::suspend();
    }

    public function close()
    {
        $this->emit('close', [$this, $this->socket]);
        $this->open = false;
        fclose($this->socket);
    }

    public function getStream()
    {
        return $this->socket;
    }

    public function __destruct()
    {
        if (is_resource($this->socket)) {
            fclose($this->socket);
        }
    }

    public function __toString(): string
    {
        return $this->socket;
    }

    private function validateSocket($socket, $fiber)
    {
        $data = stream_get_meta_data($socket);
        if ($this->isOpen() && is_resource($socket) && $data['eof']) {
            echo 'Closing' . PHP_EOL;
            $this->open = false;
            $this->close();
        }
    }
}
