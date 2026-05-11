<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

interface FlowStepConfigInterface
{
    /**
     * Returns the name of the step.
     */
    public function getName(): string;

    /**
     * Returns the closure that determines if the step should be skipped.
     */
    public function getSkip(): ?\Closure;

    /**
     * Determines if the step should be skipped based on the provided data.
     */
    public function isSkipped(mixed $data): bool;

    /**
     * Whether this step is a group (a container with child steps but not navigable itself).
     */
    public function isGroup(): bool;

    /**
     * Returns the child step configurations.
     *
     * @return array<string, self>
     */
    public function getSteps(): array;

    /**
     * Whether a child step with the given name exists (recursively).
     */
    public function hasStep(string $name): bool;

    /**
     * Returns the child step configuration with the given name (recursively).
     */
    public function getStep(string $name): self;
}
