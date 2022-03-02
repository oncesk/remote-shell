<?php

namespace Shell\Console\Input;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

class Input extends ArgvInput implements InputInterface
{
	private string $commandName;

	public function __construct(array $argv = null, string $name = null)
	{
		if ($name) {
			$this->commandName = $name;
		} else {
			reset($argv);
			$this->commandName = current($argv);
		}

		parent::__construct($argv);
	}

	public function getName(): string
	{
		return $this->commandName;
	}

	public function setDefinition(InputDefinition $definition): void
	{
		$this->definition = $definition;
		$this->bind($definition);
		$this->validate();
	}
}