<?php

namespace Shell\Console\Command;

use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class EchoCommand implements UserLandCommandInterface
{
	private InputDefinition $definition;

	public function __construct()
	{
		$this->definition = new InputDefinition([
			new InputArgument('arg', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Something to echo', []),
		]);
	}

	public function getName(): string
	{
		return 'echo';
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$output->write(implode(' ', $input->getArgument('arg')));
	}

	public function getDescription(): string
	{
		return 'Echo some text or variable';
	}

	public function getDefinition(): InputDefinition
	{
		return $this->definition;
	}
}
