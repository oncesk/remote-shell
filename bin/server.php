#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Shell\Loop\LogLoop;
use Shell\Loop\SelectLoop;
use Shell\Loop\EvLoop;
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

$socket = stream_socket_server(
    'tcp://0.0.0.0:8080',
    $errorCode,
    $errorMsg,
    STREAM_SERVER_LISTEN | STREAM_SERVER_BIND
);
stream_set_blocking($socket, 0);

if (!$socket) {
    echo $errorCode . ' - ' . $errorMsg;
    die;
}

$logger = new Monolog\Logger('default');
$logger->pushHandler(new \Monolog\Handler\StreamHandler(STDOUT));

//$loop = new LogLoop($logger, new SelectLoop());
$loop = new LogLoop($logger, new EvLoop());
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

$application = new Application($shell, $store, $loop, $commandFinder);
$application->run($server, $shell);
