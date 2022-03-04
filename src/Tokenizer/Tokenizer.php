<?php

namespace Shell\Tokenizer;

class Tokenizer implements TokenizerInterface
{
    public function tokenize(array $input): array
    {
        $output = [];

        if ($input) {
            $readingArguments = false;
            foreach ($input as $kind) {
                $output[] = [
                    'token' => $token = $this->determineToken($kind, $readingArguments),
                    'kind'  => $kind,
                ];

                $readingArguments = true;

                if ($token === Token::PIPE) {
                    $readingArguments = false;
                }
            }
        }

        return $output;
    }

    private function determineToken(string $kind, bool $doWeReadArguments): Token
    {
        if (!$doWeReadArguments) {
            return Token::COMMAND;
        }

        return '|' === $kind ? Token::PIPE : Token::ARGUMENT;
    }
}
