<?php

namespace Shell\Tests\Unit\Console\Input\Substitution;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shell\Console\Env\EnvInterface;
use Shell\Console\Input\Substitution\VarSubstitutor;
use Shell\Console\ShellInterface;

class VarSubstitutorTest extends TestCase
{
    private ShellInterface|MockObject $shell;
    private EnvInterface|MockObject $env;
    private VarSubstitutor $subject;

    protected function setUp(): void
    {
        $this->shell = $this->getMockForAbstractClass(ShellInterface::class);
        $this->env = $this->getMockForAbstractClass(EnvInterface::class);
        $this->env->method('toArray')->willReturn([
            'name' => 'John',
            'DAY'  => 'Monday',
            'file' => 'system.log',
        ]);
        $this->subject = new VarSubstitutor();
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $input
     * @param string $expected
     *
     * @return void
     */
    public function test(string $input, string $expected)
    {
        $this->assertEquals($expected, $this->subject->substitute($input, $this->env, $this->shell));
    }

    public function dataProvider(): array
    {
        return [
            ['echo $name', 'echo John'],
            ['echo $DAY', 'echo Monday'],
            ['echo $name was there on $DAY!', 'echo John was there on Monday!'],
            ['echo ($name) was there on [$DAY]!', 'echo (John) was there on [Monday]!'],
            ['cat log.log | grep $name|$DAY', 'cat log.log | grep John|Monday'],
            ['cat $file | grep $name|$DAY', 'cat system.log | grep John|Monday'],
        ];
    }
}
