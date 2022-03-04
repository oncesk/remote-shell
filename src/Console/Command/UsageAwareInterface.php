<?php

namespace Shell\Console\Command;

interface UsageAwareInterface
{
    /**
     * @return string[]
     */
    public function getUsages(): array;
}
