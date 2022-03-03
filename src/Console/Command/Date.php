<?php

namespace Shell\Console\Command;

use Shell\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Date implements UserLandCommandInterface, UsageAwareInterface, AliasAwareInterface
{
	private InputDefinition $definition;
	private string $defaultFormat = 'm-d-Y H:i';

	public function __construct()
	{
		$this->definition = new InputDefinition([
			new InputArgument('format', InputArgument::OPTIONAL, 'Format to use', $this->defaultFormat),
			new InputOption('timestamp', '-t', InputOption::VALUE_REQUIRED, 'Use timestamp as source date')
		]);
	}

	public function getName(): string
	{
		return 'date';
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$format = $input->getArgument('format');
		$timestamp = $input->getOption('timestamp');

		if ($this->isTime($input->getName())) {
			$format = $format == $this->defaultFormat ? 'H:i:s' : $format;
		} else if ($this->isDatetime($input->getName())) {
			$format = $format == $this->defaultFormat ? 'Y-m-d H:i:s' : $format;
		}

		$output->write(date($format, $timestamp ?? time()));
	}

	public function getDescription(): string
	{
		return 'Display the current time in the given FORMAT, or set the system date.';
	}

	public function getDefinition(): InputDefinition
	{
		return $this->definition;
	}

	public function getUsages(): array
	{
		return [
			'$ date',
			'$ date "D Y"',
			'$ time',
			'$ datetime',
			'$ date -t=1645608172 "y-m-d H:i"'
		];
	}

	public function getAliases(): array
	{
		return ['time', 'datetime'];
	}

	private function isDatetime(string $command): bool
	{
		return str_starts_with($command, 'datetime');
	}

	private function isTime(string $command): bool
	{
		return str_starts_with($command, 'time');
	}
}
