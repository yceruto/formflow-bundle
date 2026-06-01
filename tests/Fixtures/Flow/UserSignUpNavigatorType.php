<?php

namespace Yceruto\FormFlowBundle\Tests\Fixtures\Flow;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\Type\NavigatorFlowType;
use Yceruto\FormFlowBundle\Form\Flow\Type\NextFlowType;

class UserSignUpNavigatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('skip', NextFlowType::class, [
            'clear_submission' => true,
            'include_if' => ['professional'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'with_reset' => true,
        ]);
    }

    public function getParent(): string
    {
        return NavigatorFlowType::class;
    }
}
