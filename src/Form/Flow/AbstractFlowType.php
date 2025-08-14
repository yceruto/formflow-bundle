<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Yceruto\FormFlowBundle\Form\Flow\Type\FormFlowType;

abstract class AbstractFlowType extends AbstractType implements FormFlowTypeInterface
{
    final public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        \assert($builder instanceof FormFlowBuilderInterface);

        $this->buildFormFlow($builder, $options);
    }

    final public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        \assert($form instanceof FormFlowInterface);

        $this->buildViewFlow($view, $form, $options);
    }

    final public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        \assert($form instanceof FormFlowInterface);

        $this->finishViewFlow($view, $form, $options);
    }

    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
    }

    public function buildViewFlow(FormView $view, FormFlowInterface $form, array $options): void
    {
    }

    public function finishViewFlow(FormView $view, FormFlowInterface $form, array $options): void
    {
    }

    public function getParent(): string
    {
        return FormFlowType::class;
    }
}
