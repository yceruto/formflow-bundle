<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\Exception\BadMethodCallException;
use Symfony\Component\Form\FormTypeInterface;

class FormFlowStepBuilder implements FormFlowStepBuilderInterface
{
    private bool $locked = false;
    private int $priority = 0;
    private ?\Closure $skip = null;

    /**
     * @param class-string<FormTypeInterface> $type
     */
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private readonly array $options = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowStepConfigInterface instance.');
        }

        return $this->type;
    }

    public function getOptions(): array
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowStepConfigInterface instance.');
        }

        return $this->options;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowStepConfigInterface instance.');
        }

        $this->priority = $priority;

        return $this;
    }

    public function getSkip(): ?\Closure
    {
        return $this->skip;
    }

    public function isSkipped(mixed $data): bool
    {
        if (null === $this->skip) {
            return false;
        }

        return ($this->skip)($data);
    }

    public function setSkip(?\Closure $skip): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowStepConfigInterface instance.');
        }

        $this->skip = $skip;

        return $this;
    }

    public function getStepConfig(): FormFlowStepConfigInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowStepConfigInterface instance.');
        }

        // This method should be idempotent, so clone the builder
        $config = clone $this;
        $config->locked = true;

        return $config;
    }
}
