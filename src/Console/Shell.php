<?php

namespace Shell\Console;

use Shell\Console\Command\Finder\CommandNotFoundException;
use Shell\Console\Command\Finder\FinderInterface;
use Shell\Console\Command\Quit;
use Shell\Console\Command\StoreInterface;
use Shell\Console\Command\UserLandCommandInterface;
use Shell\Console\Input\Input;
use Shell\Console\Input\InputInterface;
use Shell\Console\Input\InputParserInterface;
use Shell\Server\ConnectionAwareInterface;
use Shell\Server\ConnectionInterface;
use Shell\Tokenizer\Token;
use Shell\Tokenizer\TokenizerInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class Shell implements ShellInterface
{
    public function __construct(
        private InputParserInterface $inputParser,
        private TokenizerInterface $tokenizer,
        private StoreInterface $store,
        private FinderInterface $commandFinder
    ) {
    }

    public function init(ConnectionInterface $connection)
    {
        $connection->write("\nRemote Shell v1.0.0\n");
        $this->writeSign($connection);
    }

    public function execute(string $input, ConnectionInterface $connection)
    {
        $inputs = $this->tokensToInputObjects(
            $this->tokenizer->tokenize(
                $this->inputParser->parse($input)
            )
        );

        if (empty($inputs)) {
            $this->writeSign($connection, false);
            return;
        }

        $output = new Output($connection);

        array_map(fn (InputInterface $input) => $this->handle($input, $output, $connection), $inputs);
    }

    private function handle(InputInterface $input, OutputInterface $output, ConnectionInterface $connection)
    {
        try {
            $command = $this->commandFinder->find($input, $this->store);

            if ($command instanceof UserLandCommandInterface) {
                $input->setDefinition($command->getDefinition());
            }

            if ($command instanceof ConnectionAwareInterface) {
                $command->setConnection($connection);
            }

            $command->execute($input, $output);
        } catch (CommandNotFoundException $e) {
            $output->write($e->getMessage());
        } catch (RuntimeException $e) {
            $output->write('<error>' . $e->getMessage() . '</error>');
        } finally {
            if (!isset($command) || !$command instanceof Quit) {
                $this->writeSign($connection);
            }
        }
    }

    private function writeSign(ConnectionInterface $connection, bool $newLineBefore = true)
    {
        $connection->write(($newLineBefore ? "\n" : '') . "\e[91m$\e[0m ");
    }

    /**
     * @param array $tokens
     * @return InputInterface[]
     */
    private function tokensToInputObjects(array $tokens): array
    {
        if (empty($tokens)) {
            return [];
        }

        $commandArgs = [];
        $inputArgs = [];

        do {
            $token = array_shift($tokens);

            switch ($token['token']) {
                case Token::COMMAND:
                    $commandArgs = [$token['kind']];
                    break;

                case Token::ARGUMENT:
                    $commandArgs[] = $token['kind'];
                    break;

                case Token::PIPE:
                    $inputArgs[] = $commandArgs;
                    break;
            }
        } while (!empty($tokens));

        $inputArgs[] = $commandArgs;

        $result = [];
        foreach ($inputArgs as $argv) {
            $result[] = new Input($argv);
        }

        return $result;
    }
}
