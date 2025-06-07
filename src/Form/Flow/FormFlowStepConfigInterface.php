<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

interface FormFlowStepConfigInterface
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
}
