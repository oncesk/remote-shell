<?php

namespace Shell\Console\Env;

class Env implements EnvInterface
{
    public function __construct(private array $vars = [])
    {}

    public function get(string $name, $default = null): ?string
    {
        return $this->vars[$name] ?? $default;
    }

    public function has(string $name): bool
    {
        return isset($this->vars[$name]);
    }

    public function set(string $name, mixed $value): void
    {
        $this->vars[$name] = (string) $value;
    }

    public function unset(string $name): void
    {
        if ($this->has($name)) {
            unset($this->vars[$name]);
        }
    }

    public function __toString(): string
    {
        return implode(
            "\n",
            array_map(fn($key, $value) => "$key=$value", array_keys($this->vars), array_values($this->vars))
        );
    }

    public function toArray(): array
    {
        return $this->vars;
    }
}
