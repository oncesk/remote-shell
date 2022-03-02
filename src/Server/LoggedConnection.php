<?php

namespace Shell\Server;

use Psr\Log\LoggerInterface;

class LoggedConnection implements ConnectionInterface
{
	public function __construct(private LoggerInterface $logger, private ConnectionInterface $connection)
	{
	}

	public function isOpen(): bool
	{
		return $this->connection->isOpen();
	}

	public function read(): ?string
	{
		$id = (string) $this->getStream();

		$this->logger->debug('Reading from ' . $id);
		$read = $this->connection->read();

		$this->logger->debug('Reading is completed from ' . $id);

		return $read;
	}

	public function write($data): void
	{
		$this->connection->write($data);
	}

	public function close()
	{
		$id = (string) $this->getStream();
		$this->logger->debug('Closing connection ' . $id);
		$this->connection->close();
		$this->logger->debug("Connection $id closed");
	}

	public function getStream()
	{
		return $this->connection->getStream();
	}

	public function __call(string $name, array $arguments)
	{
		return $this->connection->$name(...$arguments);
	}
}