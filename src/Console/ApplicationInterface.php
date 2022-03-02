<?php

namespace Shell\Console;

use Shell\Server\ServerInterface;

interface ApplicationInterface
{
	public function run(ServerInterface $server, ShellInterface $shell);
}