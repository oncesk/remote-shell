<?php

namespace Shell\Loop;

class Factory
{
    public static function create(): LoopInterface
    {
        if (class_exists(\EvLoop::class)) {
            return new EvLoop();
        }

        return new SelectLoop();
    }
}
