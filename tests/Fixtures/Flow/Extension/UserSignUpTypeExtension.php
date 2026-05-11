<?php

namespace Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\UserSignUpType;

class UserSignUpTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$builder instanceof FormFlowBuilderInterface) {
            throw new \InvalidArgumentException(\sprintf('The "%s" can only be used with FormFlowType.', self::class));
        }

        $builder->addStep('first', FormType::class, ['mapped' => false], null, 1);
        $builder->addStep('last', FormType::class, ['mapped' => false]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [UserSignUpType::class];
    }
}
