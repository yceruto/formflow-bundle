<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\Exception\BadMethodCallException;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\DataStorage\DataStorageInterface;
use Yceruto\FormFlowBundle\Form\Flow\StepAccessor\StepAccessorInterface;

/**
 * A builder for creating {@link FormFlow} instances.
 *
 * @implements \IteratorAggregate<string, FormBuilderInterface>
 */
class FormFlowBuilder extends FormBuilder implements FormFlowBuilderInterface
{
    /**
     * @var array<string, FlowStepBuilderInterface>
     */
    private array $steps = [];
    private array $initialOptions = [];
    private DataStorageInterface $dataStorage;
    private StepAccessorInterface $stepAccessor;

    public function createStepGroup(string $name): FlowStepBuilderInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        return new FlowStepBuilder($name)->setGroup(true);
    }

    public function createStep(string $name, string $type = FormType::class, array $options = []): FlowStepBuilderInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        return new FlowStepBuilder($name, $type, $options);
    }

    public function addStep(FlowStepBuilderInterface|string $name, string $type = FormType::class, array $options = [], ?callable $skip = null, int $priority = 0): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        if ($name instanceof FlowStepBuilderInterface) {
            $this->steps[$name->getName()] = $name;

            return $this;
        }

        $this->steps[$name] = $this->createStep($name, $type, $options)
            ->setSkip($skip ? $skip(...) : null)
            ->setPriority($priority)
        ;

        return $this;
    }

    public function removeStep(string $name): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        unset($this->steps[$name]);

        return $this;
    }

    public function hasStep(string $name): bool
    {
        if (isset($this->steps[$name])) {
            return true;
        }

        foreach ($this->steps as $step) {
            if ($step->hasStep($name)) {
                return true;
            }
        }

        return false;
    }

    public function getStep(string $name): FlowStepBuilderInterface
    {
        if (isset($this->steps[$name])) {
            return $this->steps[$name];
        }

        foreach ($this->steps as $step) {
            try {
                return $step->getStep($name);
            } catch (InvalidArgumentException) {
                // Continue searching
            }
        }

        throw new InvalidArgumentException(\sprintf('Step "%s" does not exist.', $name));
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setInitialOptions(array $options): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        $this->initialOptions = $options;

        return $this;
    }

    public function getInitialStep(): string
    {
        $defaultStep = $this->resolveFirstStep();

        if (!isset($this->initialOptions['data'])) {
            return $defaultStep;
        }

        return (string) $this->stepAccessor->getStep($this->initialOptions['data'], $defaultStep);
    }

    public function getInitialOptions(): array
    {
        return $this->initialOptions;
    }

    public function setDataStorage(DataStorageInterface $dataStorage): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        $this->dataStorage = $dataStorage;

        // make sure the current data is available immediately
        $this->setData($dataStorage->load($this->getData()));

        return $this;
    }

    public function getDataStorage(): DataStorageInterface
    {
        return $this->dataStorage;
    }

    public function setStepAccessor(StepAccessorInterface $stepAccessor): static
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        $this->stepAccessor = $stepAccessor;

        return $this;
    }

    public function getStepAccessor(): StepAccessorInterface
    {
        return $this->stepAccessor;
    }

    public function isAutoReset(): bool
    {
        return $this->getOption('auto_reset');
    }

    public function getFormConfig(): FormFlowConfigInterface
    {
        /** @var self $config */
        $config = parent::getFormConfig();

        foreach ($config->steps as $name => $step) {
            $config->steps[$name] = $step->getStepConfig();
        }

        return $config;
    }

    public function getForm(): FormFlowInterface
    {
        if ($this->locked) {
            throw new BadMethodCallException('FormFlowBuilder methods cannot be accessed anymore once the builder is turned into a FormFlowConfigInterface instance.');
        }

        $flow = $this->createFormFlow();

        foreach ($this->all() as $child) {
            if ($child instanceof FormFlowBuilderInterface) {
                throw new LogicException('Nested form flows is not currently supported.');
            }

            // Automatic initialization is only supported on root forms
            $flow->add($child->setAutoInitialize(false)->getForm());
        }

        if ($this->getAutoInitialize()) {
            // Automatically initialize the form if it is configured so
            $flow->initialize();
        }

        return $flow;
    }

    private function createFormFlow(): FormFlowInterface
    {
        if (!$this->steps) {
            throw new InvalidArgumentException('Steps not configured.');
        }

        uasort($this->steps, static fn (FlowStepBuilderInterface $a, FlowStepBuilderInterface $b) => $b->getPriority() <=> $a->getPriority());

        $config = $this->getFormConfig();
        $currentStep = $this->resolveCurrentStep();

        $step = $this->getStep($currentStep);
        $this->add($step->getName(), $step->getType(), $step->getOptions());

        $cursor = new FlowCursor($config->getSteps(), $currentStep);
        $this->pruneActionButtons($this, $cursor);

        return new FormFlow($config, $cursor);
    }

    private function resolveCurrentStep(): string
    {
        $data = $this->getData();

        if (!$currentStep = $this->getStepAccessor()->getStep($data)) {
            $currentStep = $this->resolveFirstStep();
            $this->getStepAccessor()->setStep($data, $currentStep);
            $this->setData($data);
        }

        return $currentStep;
    }

    /**
     * Finds the first navigable step in DFS pre-order.
     *
     * A step is navigable if it is neither a group nor skipped.
     */
    private function resolveFirstStep(?array $steps = null): string
    {
        foreach ($steps ?? $this->steps as $step) {
            if (!$step->isGroup() && !$step->isSkipped($this->getData())) {
                return $step->getName();
            }

            if ($children = $step->getSteps()) {
                try {
                    return $this->resolveFirstStep($children);
                } catch (LogicException) {
                    continue;
                }
            }
        }

        throw new LogicException('No navigable step found. All steps are groups or skipped.');
    }

    private function pruneActionButtons(FormBuilderInterface $builder, FlowCursor $cursor): void
    {
        foreach ($builder->all() as $child) {
            if ($child->count() > 0) {
                $this->pruneActionButtons($child, $cursor);

                continue;
            }

            if (!$child instanceof FlowButtonBuilder || !\is_callable($include = $child->getOption('include_if'))) {
                continue;
            }

            if (!$include($cursor)) {
                $builder->remove($child->getName());
            }
        }
    }
}
