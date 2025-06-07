<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Yceruto\FormFlowBundle\Form\Flow\DataStorage\DataStorageInterface;
use Yceruto\FormFlowBundle\Form\Flow\StepAccessor\StepAccessorInterface;
use Symfony\Component\Form\FormConfigInterface;

/**
 * The configuration of a {@link FormFlow} object.
 */
interface FormFlowConfigInterface extends FormConfigInterface
{
    /**
     * Checks if a step with the given name exists.
     */
    public function hasStep(string $name): bool;

    /**
     * Returns the step with the given name.
     */
    public function getStep(string $name): FormFlowStepConfigInterface;

    /**
     * Returns all steps.
     *
     * @return array<string, FormFlowStepConfigInterface>
     */
    public function getSteps(): array;

    /**
     * Returns the name of the initial step.
     */
    public function getInitialStep(): string;

    /**
     * Returns the initial options for the form flow.
     *
     * @return array<string, mixed>
     */
    public function getInitialOptions(): array;

    /**
     * Returns the data storage for the form flow.
     */
    public function getDataStorage(): DataStorageInterface;

    /**
     * Returns the step accessor for the form flow.
     */
    public function getStepAccessor(): StepAccessorInterface;

    /**
     * Checks if the form flow is configured to auto reset once it's finished.
     */
    public function isAutoReset(): bool;
}
