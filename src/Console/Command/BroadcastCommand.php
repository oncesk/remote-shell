<?php

namespace Shell\Console\Command;

use Shell\Console\Input\InputInterface;
use Shell\Server\ConnectionAwareInterface;
use Shell\Server\ConnectionInterface;
use Shell\Server\ServerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class BroadcastCommand implements UserLandCommandInterface, ConnectionAwareInterface, UsageAwareInterface
{
	private ?ConnectionInterface $connection;

	public function __construct(private ServerInterface $server)
	{
	}


	public function getName(): string
	{
		return 'broadcast';
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		foreach ($this->server->getConnections() as $connection) {
			if ($connection !== $this->connection) {
				$connection->write(sprintf(
					"\nBroadcast message received\n\n%s\n",
					$input->getArgument('message')
				));
			}
		}
	}

	public function getDescription(): string
	{
		return 'Broadcast message to all clients';
	}

	public function getDefinition(): InputDefinition
	{
		return new InputDefinition([new InputArgument('message', InputArgument::REQUIRED, 'Message to send')]);
	}

	public function getConnection(): ?ConnectionInterface
	{
		return $this->connection;
	}

	public function setConnection(ConnectionInterface $connection): void
	{
		$this->connection = $connection;
	}

	public function getUsages(): array
	{
		return [
			'broadcast Hello',
			'broadcast "Hello user! How do you do?"',
		];
	}
}
