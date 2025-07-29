<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

interface FlowStepBuilderInterface extends FlowStepConfigInterface
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
     * Returns a FlowStepConfigInterface instance for the step.
     */
    public function getStepConfig(): FlowStepConfigInterface;
}
