<?php

namespace Shell\Tokenizer;

interface TokenizerInterface
{
	final public const COMMAND = 1;
	final public const ARGUMENT = 2;
	final public const PIPE = 3;

	/**
	 * @param array $input
	 *
	 * @return array
	 */
	public function tokenize(array $input): array;
}
