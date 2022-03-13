<?php

namespace Shell\Loop;

use Psr\Log\LoggerInterface;

class LogLoop implements LoopInterface
{
    public function __construct(private LoggerInterface $logger, private LoopInterface $loop)
    {
    }

    public function run(): void
    {
        $this->logger->debug('Loop : Starting the loop ' . $this->loop::class);
        $this->loop->run();
    }

    public function stop(): void
    {
        $this->logger->debug('Loop : Stopping the loop ');
        $this->loop->stop();
        $this->logger->debug('Loop : Done');
    }

    public function read($resource, \Closure $closure): LoopInterface
    {
        $this->logger->debug('Loop : Set read callback for ' . $resource);
        $this->loop->read($resource, $closure);

        return $this;
    }

    public function write($resource, \Closure $closure): LoopInterface
    {
        $this->logger->debug('Loop : Set write callback for ' . $resource);
        $this->loop->write($resource, $closure);

        return $this;
    }

    public function removeRead($resource): LoopInterface
    {
        $this->logger->debug('Loop : Remove read callback for ' . $resource);
        $this->loop->removeRead($resource);

        return $this;
    }

    public function removeWrite($resource): LoopInterface
    {
        $this->logger->debug('Loop : Remove write callback for ' . $resource);
        $this->loop->removeWrite($resource);

        return $this;
    }

    public function always(\Closure $closure): LoopInterface
    {
        $this->logger->debug('Loop : Set always callback');
        $this->loop->always($closure);

        return $this;
    }

    public function tick(\Closure $closure): LoopInterface
    {
        $this->logger->debug('Loop : Set tick ');
        $this->loop->tick($closure);

        return $this;
    }
}
