<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractFlowType;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\Type\NavigatorFlowType;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Form\Data\RegistrationDto;

/**
 * A nested flow: an "account" group (credentials + security) followed by a top-level "profile" step.
 */
class RegistrationType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder->addStep(
            $builder->createStepGroup('account')
                ->addStep('credentials', AccountCredentialsType::class)
                ->addStep('security', AccountSecurityType::class)
        );
        $builder->addStep('profile', ProfileType::class);

        $builder->add('navigator', NavigatorFlowType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationDto::class,
            'step_property_path' => 'step',
        ]);
    }
}
