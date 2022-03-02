<?php

namespace Shell\Console\Input;

interface InputParserInterface
{
	public function parse(string $input): array;
}