<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\FormInterface;

interface FormFlowInterface extends FormInterface
{
    /**
     * Returns the action button clicked during form submission.
     */
    public function getClickedActionButton(): ?ActionButtonInterface;

    /**
     * Resets the flow by clearing stored data and setting the cursor to the initial step.
     */
    public function reset(): void;

    /**
     * Moves back to a previous step in the flow.
     *
     * @param string|null $step The step to move back to, or null to move back one step
     *
     * @throws RuntimeException If the previous step cannot be determined
     */
    public function moveBack(?string $step = null): void;

    /**
     * Moves to the next step in the flow.
     *
     * @throws RuntimeException If the next step cannot be determined
     */
    public function moveNext(): void;

    /**
     * Creates a new form for the current step with initial options.
     */
    public function newStepForm(): static;

    /**
     * Gets the form for the current step, handling any action if needed.
     * Returns a new step form if the current form is valid and submitted.
     */
    public function getStepForm(): static;

    /**
     * Returns the cursor that tracks the current position in the flow.
     */
    public function getCursor(): FormFlowCursor;

    /**
     * Returns the configuration for this flow.
     */
    public function getConfig(): FormFlowConfigInterface;

    /**
     * Checks if the flow has been completed.
     */
    public function isFinished(): bool;
}
