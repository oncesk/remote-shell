<?php

namespace Shell\Console\Input\Tokenizer;

interface TokenizerInterface
{
    /**
     * @param array $input
     *
     * @return string[]
     */
    public function tokenize(array $input): array;
}
