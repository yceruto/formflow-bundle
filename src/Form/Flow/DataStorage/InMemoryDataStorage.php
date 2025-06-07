<?php

namespace Yceruto\FormFlowBundle\Form\Flow\DataStorage;

class InMemoryDataStorage implements DataStorageInterface
{
    private array $memory = [];

    public function __construct(
        private readonly string $key,
    ) {
    }

    public function save(object|array $data): void
    {
        $this->memory[$this->key] = $data;
    }

    public function load(object|array|null $default = null): object|array|null
    {
        return $this->memory[$this->key] ?? $default;
    }

    public function clear(): void
    {
        unset($this->memory[$this->key]);
    }
}
