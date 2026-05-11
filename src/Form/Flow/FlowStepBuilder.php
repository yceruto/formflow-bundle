<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\Exception\BadMethodCallException;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormTypeInterface;

class FlowStepBuilder implements FlowStepBuilderInterface
{
    private bool $locked = false;
    private int $priority = 0;
    private ?\Closure $skip = null;
    /** @var array<string, FlowStepBuilderInterface> */
    private array $children = [];
    private bool $group = false;

    /**
     * @param class-string<FormTypeInterface> $type
     */
    public function __construct(
        private readonly string $name,
        private readonly string $type = FormType::class,
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
            throw new BadMethodCallException('FlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FlowStepConfigInterface instance.');
        }

        return $this->type;
    }

    public function getOptions(): array
    {
        if ($this->locked) {
            throw new BadMethodCallException('FlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FlowStepConfigInterface instance.');
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
            throw new BadMethodCallException('FlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FlowStepConfigInterface instance.');
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
            throw new BadMethodCallException('FlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FlowStepConfigInterface instance.');
        }

        $this->skip = $skip;

        return $this;
    }

    public function setGroup(bool $group): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FlowStepConfigInterface instance.');
        }

        $this->group = $group;

        return $this;
    }

    public function isGroup(): bool
    {
        return $this->group;
    }

    public function addStep(FlowStepBuilderInterface|string $name, string $type = FormType::class, array $options = [], ?callable $skip = null, int $priority = 0): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FlowStepConfigInterface instance.');
        }

        if ($name instanceof FlowStepBuilderInterface) {
            $this->children[$name->getName()] = $name;

            return $this;
        }

        $this->children[$name] = new FlowStepBuilder($name, $type, $options)
            ->setSkip($skip ? $skip(...) : null)
            ->setPriority($priority);

        return $this;
    }

    public function removeStep(string $name): static
    {
        unset($this->children[$name]);

        return $this;
    }

    public function getSteps(): array
    {
        return $this->children;
    }

    public function hasStep(string $name): bool
    {
        return isset($this->children[$name]) || array_any($this->children, fn (FlowStepBuilderInterface $step) => $step->hasStep($name));
    }

    public function getStep(string $name): FlowStepConfigInterface
    {
        if (isset($this->children[$name])) {
            return $this->children[$name];
        }

        foreach ($this->children as $step) {
            try {
                return $step->getStep($name);
            } catch (InvalidArgumentException) {
                // Continue searching
            }
        }

        throw new InvalidArgumentException(\sprintf('Sub step "%s" does not exist in "%s" step.', $name, $this->name));
    }

    public function getStepConfig(): FlowStepConfigInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('FlowStepBuilder methods cannot be accessed anymore once the builder is turned into a FlowStepConfigInterface instance.');
        }

        // This method should be idempotent, so clone the builder
        $config = clone $this;
        $config->locked = true;

        uasort($config->children, static fn (FlowStepBuilderInterface $a, FlowStepBuilderInterface $b) => $b->getPriority() <=> $a->getPriority());

        foreach ($config->children as $name => $step) {
            $config->children[$name] = $step->getStepConfig();
        }

        return $config;
    }
}
