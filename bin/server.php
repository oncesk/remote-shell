#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Shell\Console\Application;
use Shell\Console\Command\Finder\ByName;
use Shell\Console\Command\Finder\Finder;
use Shell\Console\Command\Finder\MatchableFinder;
use Shell\Console\Command\Store;
use Shell\Console\Input\Substitution\Substitutor;
use Shell\Console\Input\Substitution\VarSubstitutor;
use Shell\Console\Input\InputParser;
use Shell\Console\Input\Tokenizer\Tokenizer;
use Shell\Console\Shell;
use Shell\Loop\Factory;
use Shell\Loop\LogLoop;
use Shell\Server\CallableConnectionFactory;
use Shell\Server\Connection;
use Shell\Server\LoggedConnection;
use Shell\Server\LoggedServer;
use Shell\Server\Server;

$isDebug = (bool) (getenv('SHELL_DEBUG') ?? false);

$logger = new Monolog\Logger('default');
$logger->pushHandler(
    $isDebug ? new \Monolog\Handler\StreamHandler(STDOUT) : new \Monolog\Handler\NullHandler()
);

$host = getenv('SHELL_HOST');
$port = getenv('SHELL_PORT');

$address = sprintf(
    'tcp://%s:%d',
    $host ? $host : 'localhost',
    $port ? $port : 80,
);

$socket = stream_socket_server(
    $address,
    $errorCode,
    $errorMsg,
    STREAM_SERVER_LISTEN | STREAM_SERVER_BIND
);

if (!$socket) {
    echo $errorCode . ' - ' . $errorMsg;
    exit(1);
}

stream_set_blocking($socket, 0);

$logger->debug('Listening ' . $address);

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
    $commandFinder,
    new Substitutor([
        new VarSubstitutor(),
    ])
);

try {
    $application = new Application($shell, $store, $loop, $commandFinder);
    $application->run($server, $shell);
} catch (\Exception $exception) {
    echo $exception->getMessage();
    exit($exception->getCode() ?? 1);
}
