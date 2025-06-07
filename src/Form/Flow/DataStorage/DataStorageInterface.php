<?php

namespace Yceruto\FormFlowBundle\Form\Flow\DataStorage;

/**
 * Handles storing and retrieving form data between steps.
 */
interface DataStorageInterface
{
    public function save(object|array $data): void;

    public function load(object|array|null $default = null): object|array|null;

    public function clear(): void;
}
