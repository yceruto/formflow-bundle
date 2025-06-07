<?php

namespace Yceruto\FormFlowBundle\Form\Flow\StepAccessor;

/**
 * Reads from or writes the current step name to a provided data source.
 */
interface StepAccessorInterface
{
    public function getStep(object|array $data, ?string $default = null): ?string;

    public function setStep(object|array &$data, string $step): void;
}
