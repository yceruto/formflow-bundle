<?php

namespace Yceruto\FormFlowBundle\Form\Flow\DataStorage;

final class NullDataStorage implements DataStorageInterface
{
    public function save(object|array $data): void
    {
        // no-op
    }

    public function load(object|array|null $default = null): object|array|null
    {
        return $default;
    }

    public function clear(): void
    {
        // no-op
    }
}
