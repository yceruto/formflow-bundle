<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\Type\FormFlowType;

abstract class AbstractFlowType extends AbstractType implements FormFlowTypeInterface
{
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        \assert($builder instanceof FormFlowBuilderInterface);

        $this->buildFormFlow($builder, $options);
    }

    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
    }

    public function getParent(): string
    {
        return FormFlowType::class;
    }
}
