<?php

namespace Shell\Tests\Unit\Console\Input\Tokenizer;

use PHPUnit\Framework\TestCase;
use Shell\Console\Input\Tokenizer\Token;
use Shell\Console\Input\Tokenizer\Tokenizer;

class TokenizerTest extends TestCase
{
	/**
	 * @dataProvider dataProvider
	 *
	 * @param array $input
	 * @param array $output
	 *
	 * @return void
	 */
	public function test(array $input, array $output)
	{
		$subject = new Tokenizer();
		$this->assertEquals($output, $subject->tokenize($input));
	}

	public function dataProvider(): \Generator
	{
		yield [['help'], [['token' => Token::COMMAND, 'kind' => 'help']]];

		yield [
			[
				'help',
				'-h',
			],
			[
				['token' => Token::COMMAND, 'kind' => 'help'],
				['token' => Token::ARGUMENT, 'kind' => '-h'],
			],
		];

		yield [
			[
				'echo',
				'Super long argument',
			],
			[
				['token' => Token::COMMAND, 'kind' => 'echo'],
				['token' => Token::ARGUMENT, 'kind' => 'Super long argument'],
			],
		];

		yield [
			[
				'truncate',
				'-s',
				'0',
				'/var/log/messages',
			],
			[
				['token' => Token::COMMAND, 'kind' => 'truncate'],
				['token' => Token::ARGUMENT, 'kind' => '-s'],
				['token' => Token::ARGUMENT, 'kind' => '0'],
				['token' => Token::ARGUMENT, 'kind' => '/var/log/messages'],
			],
		];

		yield [
			[
				'cat',
				'/var/log/messages',
				'|',
				'grep',
				'error',
			],
			[
				['token' => Token::COMMAND, 'kind' => 'cat'],
				['token' => Token::ARGUMENT, 'kind' => '/var/log/messages'],
				['token' => Token::PIPE, 'kind' => '|'],
				['token' => Token::COMMAND, 'kind' => 'grep'],
				['token' => Token::ARGUMENT, 'kind' => 'error'],
			],
		];

        yield [
            [
                'echo',
                'Hello',
                '>',
                'file',
            ],
            [
                ['token' => Token::COMMAND, 'kind' => 'echo'],
                ['token' => Token::ARGUMENT, 'kind' => 'Hello'],
                ['token' => Token::GREATER, 'kind' => '>'],
                ['token' => Token::COMMAND, 'kind' => 'file'],
                ['token' => Token::ARGUMENT, 'kind' => 'error'],
            ],
        ];
	}
}
