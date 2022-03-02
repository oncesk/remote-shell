<?php

namespace Shell\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Pipe implements CommandInterface
{
	/**
	 * @var CommandInterface[]
	 */
	private array $commands;
	private array $arguments;

	public function __construct(array $commands, array $arguments)
	{
		$this->commands = $commands;
		$this->arguments = $arguments;
	}


	public function execute(InputInterface $input, OutputInterface $output)
	{
		// TODO: Implement execute() method.
	}
}