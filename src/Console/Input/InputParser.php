<?php

namespace Shell\Console\Input;

class InputParser implements InputParserInterface
{
    public function parse(string $input): array
    {
        return \Clue\Arguments\split($input);
    }
}
