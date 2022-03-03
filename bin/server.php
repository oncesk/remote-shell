#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Shell\Loop\LogLoop;
use Shell\Loop\Factory;
use Shell\Server\LoggedServer;
use Shell\Server\Server;
use Shell\Server\Connection;
use Shell\Server\LoggedConnection;
use Shell\Server\CallableConnectionFactory;
use Shell\Console\Command\Store;
use Shell\Console\Shell;
use Shell\Console\Input\InputParser;
use Shell\Tokenizer\Tokenizer;
use Shell\Console\Command\Finder\Finder;
use Shell\Console\Command\Finder\ByName;
use Shell\Console\Command\Finder\MatchableFinder;
use Shell\Console\Application;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$isDebug = (bool) (getenv('SHELL_DEBUG') ?? false);

$logger = new Monolog\Logger('default');
$logger->pushHandler(
    $isDebug ? new \Monolog\Handler\StreamHandler(STDOUT) : new \Monolog\Handler\NullHandler()
);

$address = sprintf(
    'tcp://%s:%d',
    getenv('SHELL_HOST') ?? 'localhost',
    getenv('SHELL_PORT') ?? 80,
);

$socket = stream_socket_server(
    $address,
    $errorCode,
    $errorMsg,
    STREAM_SERVER_LISTEN | STREAM_SERVER_BIND
);
stream_set_blocking($socket, 0);

$logger->debug('Listening ' . $address);

if (!$socket) {
    echo $errorCode . ' - ' . $errorMsg;
    die;
}

$loop = new LogLoop($logger, Factory::create());
$connectionFactory = new CallableConnectionFactory(function () use ($logger) {
    return new LoggedConnection(
	    $logger,
        new Connection(...func_get_args())
    );
});
$server = new LoggedServer(
    $logger,
    new Server($loop, $socket, $connectionFactory),
);
$store = new Store();
$commandFinder = new Finder(
	new ByName(),
	new MatchableFinder()
);
$shell = new Shell(
    new InputParser(),
	new Tokenizer(),
    $store,
    $commandFinder
);

try {
    $application = new Application($shell, $store, $loop, $commandFinder);
    $application->run($server, $shell);
} catch (\Exception $exception) {
    echo $exception->getMessage();
    exit($exception->getCode() ?? 1);
}
