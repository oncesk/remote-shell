<?php

namespace Shell\Console\Command;

use Shell\Console\Input\InputInterface;
use Shell\Server\ConnectionAwareInterface;
use Shell\Server\ConnectionInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Quit extends CallableCommand implements ConnectionAwareInterface, AliasAwareInterface
{
	private ?ConnectionInterface $connection;

	public function __construct()
	{
		parent::__construct('quit', 'Close connection and quit', $this->quit(...));
	}

	protected function quit(InputInterface $input, OutputInterface $output)
	{
		$output->write('Bue Bue!', true);
		$this->getConnection()->close();
	}

	public function getConnection(): ?ConnectionInterface
	{
		return $this->connection;
	}

	public function setConnection(ConnectionInterface $connection): void
	{
		$this->connection = $connection;
	}

    public function getAliases(): array
    {
        return ['exit'];
    }
}
