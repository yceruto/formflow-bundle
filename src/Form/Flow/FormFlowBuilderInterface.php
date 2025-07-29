<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\DataStorage\DataStorageInterface;
use Yceruto\FormFlowBundle\Form\Flow\StepAccessor\StepAccessorInterface;

/**
 * @extends \Traversable<string, FormBuilderInterface>
 */
interface FormFlowBuilderInterface extends FormBuilderInterface, FormFlowConfigInterface
{
    /**
     * Creates a new step builder.
     */
    public function createStep(string $name, string $type = FormType::class, array $options = []): FlowStepBuilderInterface;

    /**
     * Adds a step to the form flow.
     */
    public function addStep(FlowStepBuilderInterface|string $name, string $type = FormType::class, array $options = [], ?callable $skip = null, int $priority = 0): static;

    /**
     * Removes a step from the form flow.
     */
    public function removeStep(string $name): static;

    /**
     * Returns a step builder by name.
     */
    public function getStep(string $name): FlowStepBuilderInterface;

    /**
     * Returns all step builders.
     *
     * @return array<string, FlowStepBuilderInterface>
     */
    public function getSteps(): array;

    /**
     * Sets the initial options for the form flow.
     *
     * @param array<string, mixed> $options
     */
    public function setInitialOptions(array $options): static;

    /**
     * Sets the data storage for the form flow.
     */
    public function setDataStorage(DataStorageInterface $dataStorage): static;

    /**
     * Sets the step accessor for the form flow.
     */
    public function setStepAccessor(StepAccessorInterface $stepAccessor): static;

    /**
     * Creates and returns the form flow instance.
     */
    public function getForm(): FormFlowInterface;
}
