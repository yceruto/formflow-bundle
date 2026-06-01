<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\Extension\Core\Type\FormType;

interface StepFlowBuilderConfigInterface extends StepFlowConfigInterface
{
    /**
     * Returns the form type class name for the step.
     */
    public function getType(): string;

    /**
     * Returns the form options for the step.
     */
    public function getOptions(): array;

    /**
     * Returns the priority of the step.
     */
    public function getPriority(): int;

    /**
     * Sets the priority of the step.
     */
    public function setPriority(int $priority): static;

    /**
     * Sets the closure that determines if the step should be skipped.
     */
    public function setSkip(?\Closure $skip): static;

    /**
     * Marks (or unmarks) this step as a group (a non-navigable container with child steps).
     */
    public function setGroup(bool $group): static;

    /**
     * Adds a child step.
     */
    public function addStep(self|string $name, string $type = FormType::class, array $options = [], ?callable $skip = null, int $priority = 0): static;

    /**
     * Removes a child step by name.
     */
    public function removeStep(string $name): static;

    /**
     * Returns a StepFlowConfigInterface instance for the step.
     */
    public function getStepConfig(): StepFlowConfigInterface;
}
