<?php

namespace Shell\Console\Command;

use Symfony\Component\Console\Input\InputDefinition;

interface UserLandCommandInterface extends CommandInterface
{
	public function getDescription(): string;
	public function getDefinition(): InputDefinition;
}