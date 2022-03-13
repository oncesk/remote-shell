<?php

namespace Shell\Tests\Unit\Console\Input;

use PHPUnit\Framework\TestCase;
use Shell\Console\Input\InputParser;

class InputParserTest extends TestCase
{
	/**
	 * @dataProvider dataProvider
	 *
	 * @param string $input
	 * @param array $output
	 *
	 * @return void
	 */
	public function test(string $input, array $output)
	{
		$parser = new InputParser();

		$this->assertEquals($output, $parser->parse($input));
	}

	public function dataProvider(): array
	{
		return [
			[
				'help',
				[
					'help',
				],
			],
			[
				'cat /var/log',
				[
					'cat',
					'/var/log',
				],
			],
			[
				'echo "Hello World!"',
				[
					'echo',
					'Hello World!',
				],
			],
			[
				'echo -s "Hello World!"',
				[
					'echo',
					'-s',
					'Hello World!',
				],
			],
		];
	}
}
