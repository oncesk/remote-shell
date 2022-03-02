<?php

namespace Shell\Console\Command;

use Shell\Console\Command\Finder\FinderInterface;
use Shell\Console\Descriptor\TextDescription;
use Shell\Console\Input\Input;
use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand implements UserLandCommandInterface
{
	private InputDefinition $definition;

	public function __construct(private StoreInterface $store, private FinderInterface $commandFinder)
	{
		$this->definition = new InputDefinition([
			new InputArgument('command', InputArgument::OPTIONAL, 'Show command help')
		]);
	}

	public function getName(): string
	{
		return 'help';
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$command = $input->getArgument('command');
		if ($command) {
			$this->showCommandHelp(
				$this->commandFinder->find(new Input([], $command), $this->store),
				$output
			);
		} else {
			$this->showAllCommandsHelp($input, $output);
		}
	}

	public function getDescription(): string
	{
		return 'Shows all commands';
	}

	public function getDefinition(): InputDefinition
	{
		return $this->definition;
	}

	private function showAllCommandsHelp(InputInterface $input, OutputInterface $output)
	{
		$message = "\n\nAvailable commands\n";

		foreach ($this->store->getAll() as $command) {
			if ($command instanceof UserLandCommandInterface) {
				$message .= sprintf(
					"\n  %s - %s",
					$command->getName(),
					$command->getDescription()
				);
			}
		}

		$output->write($message, true);
	}

	private function showCommandHelp(CommandInterface $command, OutputInterface $output)
	{
		$description = new TextDescription();
		$description->describe($output, $command);
	}
}
