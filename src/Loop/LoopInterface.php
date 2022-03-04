<?php

namespace Shell\Loop;

interface LoopInterface
{
    public function run(): void;
    public function stop(): void;

    public function read($resource, \Closure $closure): LoopInterface;
    public function write($resource, \Closure $closure): LoopInterface;

    public function removeRead($resource): LoopInterface;
    public function removeWrite($resource): LoopInterface;

    public function always(\Closure $closure): LoopInterface;
    public function tick(\Closure $closure): LoopInterface;
}
