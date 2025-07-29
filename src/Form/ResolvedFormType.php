<?php

namespace Yceruto\FormFlowBundle\Form;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormType as SymfonyResolvedFormType;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonBuilder;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonTypeInterface;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilder;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowTypeInterface;

final class ResolvedFormType extends SymfonyResolvedFormType
{
    public function __construct(
        private readonly FormTypeInterface $innerType,
        array $typeExtensions = [],
        ?ResolvedFormTypeInterface $parent = null,
    ) {
        parent::__construct($innerType, $typeExtensions, $parent);
    }

    /**
     * @throws ExceptionInterface
     */
    public function createBuilder(FormFactoryInterface $factory, string $name, array $options = []): FormBuilderInterface
    {
        $builder = parent::createBuilder($factory, $name, $options);

        if ($builder instanceof FormFlowBuilderInterface) {
            $builder->setInitialOptions($options);
        }

        return $builder;
    }

    protected function newBuilder(string $name, ?string $dataClass, FormFactoryInterface $factory, array $options): FormBuilderInterface
    {
        if ($this->innerType instanceof FlowButtonTypeInterface) {
            return new FlowButtonBuilder($name, $options);
        }

        if ($this->innerType instanceof FormFlowTypeInterface) {
            return new FormFlowBuilder($name, $dataClass, new EventDispatcher(), $factory, $options);
        }

        return parent::newBuilder($name, $dataClass, $factory, $options);
    }
}
