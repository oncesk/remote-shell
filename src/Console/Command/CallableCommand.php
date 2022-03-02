<?php

namespace Shell\Console\Command;

use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class CallableCommand implements UserLandCommandInterface
{
	private InputDefinition $definition;

	public function __construct(
		private string $name,
		private string $description,
		private \Closure $callback,
		InputDefinition $definition = null
	) {
		$this->definition = $definition ?? new InputDefinition();
	}


	public function getName(): string
	{
		return $this->name;
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		($this->callback)($input, $output);
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getDefinition(): InputDefinition
	{
		return $this->definition;
	}
}