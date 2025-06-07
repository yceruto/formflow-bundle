<?php

namespace Yceruto\FormFlowBundle\Form\Flow\StepAccessor;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class PropertyPathStepAccessor implements StepAccessorInterface
{
    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly PropertyPathInterface $propertyPath,
    ) {
    }

    public function getStep(object|array $data, ?string $default = null): ?string
    {
        return $this->propertyAccessor->getValue($data, $this->propertyPath) ?: $default;
    }

    public function setStep(object|array &$data, string $step): void
    {
        $this->propertyAccessor->setValue($data, $this->propertyPath, $step);
    }
}
