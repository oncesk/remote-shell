<?php

namespace Shell\Console\Command;

interface AliasAwareInterface
{
    /**
     * @return string[]
     */
    public function getAliases(): array;
}
