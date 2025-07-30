<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\FormTypeInterface;

/**
 * A type that should be converted into a {@link FormFlow} instance.
 */
interface FormFlowTypeInterface extends FormTypeInterface
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void;
}
