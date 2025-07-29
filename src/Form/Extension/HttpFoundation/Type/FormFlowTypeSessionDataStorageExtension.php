<?php

namespace Yceruto\FormFlowBundle\Form\Extension\HttpFoundation\Type;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Yceruto\FormFlowBundle\Form\Flow\DataStorage\SessionDataStorage;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\Type\FormFlowType;

class FormFlowTypeSessionDataStorageExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly ?RequestStack $requestStack = null,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        \assert($builder instanceof FormFlowBuilderInterface);

        if (null === $this->requestStack || null !== $options['data_storage']) {
            return;
        }

        $key = \sprintf('_sf_formflow.%s_%s', strtolower(str_replace('\\', '_', $builder->getType()->getInnerType()::class)), $builder->getName());
        $builder->setDataStorage(new SessionDataStorage($key, $this->requestStack));
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormFlowType::class];
    }
}
