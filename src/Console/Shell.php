<?php

namespace Shell\Console;

use Shell\Console\Command\Finder\FinderInterface;
use Shell\Console\Command\Quit;
use Shell\Console\Command\StoreInterface;
use Shell\Console\Command\UserLandCommandInterface;
use Shell\Console\Env\Env;
use Shell\Console\Env\EnvAwareInterface;
use Shell\Console\Input\Input;
use Shell\Console\Input\InputInterface;
use Shell\Console\Input\InputParserInterface;
use Shell\Console\Input\Substitution\SubstitutorInterface;
use Shell\Console\Input\Tokenizer\Token;
use Shell\Console\Input\Tokenizer\TokenizerInterface;
use Shell\Console\Output\Output;
use Shell\Console\Output\WrittenInterface;
use Shell\Server\ConnectionAwareInterface;
use Shell\Server\ConnectionInterface;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

class Shell implements ShellInterface
{
    private \SplObjectStorage $environments;

    public function __construct(
        private InputParserInterface $inputParser,
        private TokenizerInterface $tokenizer,
        private StoreInterface $store,
        private FinderInterface $commandFinder,
        private SubstitutorInterface $substitutor,
    ) {
        $this->environments = new \SplObjectStorage();
    }

    public function init(ConnectionInterface $connection)
    {
        $this->environments[$connection] = new Env([
            'DIR' => '/',
        ]);

        $connection->write("\nRemote Shell v1.0.0\n");
        $this->writeSign($connection);
    }

    public function execute(string $input, ConnectionInterface $connection)
    {
        //  make inputs
        $inputs = $this->tokensToInputObjects(

            //  tokenize
            $this->tokenizer->tokenize(

                //  parse input to array
                $this->inputParser->parse(

                    //  substitute variables | execute nested commands
                    $this->substitutor->substitute($input, $this->environments[$connection], $this)
                )
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

            if ($command instanceof EnvAwareInterface) {
                $command->setEnvironment($this->environments[$connection]);
            }

            $command->execute($input, $output);
        } catch (RuntimeException $e) {
            $output->write('<error>' . $e->getMessage() . '</error>');
        } catch (\Throwable $e) {
            $output->write($e->getMessage());
        } finally {
            if (!isset($command) || !$command instanceof Quit) {
                $nl = $output instanceof WrittenInterface ? $output->getWrittenLength() : true;

                $this->writeSign($connection, $nl);
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

        return array_map(fn(array $argv) => new Input($argv), $inputArgs);
    }
}
