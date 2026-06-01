<?php

namespace Yceruto\FormFlowBundle\Tests\Fixtures\Flow;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractFlowType;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\Type\NavigatorFlowType;

class LastStepSkippedType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder->addStep('step1', TextType::class);
        $builder->addStep('step2', FormType::class, [], static fn () => true);

        $builder->add('navigator', NavigatorFlowType::class, [
            'with_reset' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'step_property_path' => '[currentStep]',
        ]);
    }
}
