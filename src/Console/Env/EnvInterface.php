<?php

namespace Shell\Console\Env;

interface EnvInterface extends \Stringable
{
    public function get(string $name, $default = null): ?string;
    public function has(string $name): bool;
    public function set(string $name, mixed $value): void;
    public function unset(string $name): void;
    public function toArray(): array;
}
