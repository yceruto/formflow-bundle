<?php

namespace Yceruto\FormFlowBundle\Tests\Fixtures\Flow;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractFlowType;
use Yceruto\FormFlowBundle\Form\Flow\DataStorage\InMemoryDataStorage;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Form\Flow\Type\NavigatorFlowType;

/**
 * a(group) => [a1, a2], b, c(group) => [c1].
 */
class GroupingStepsFlowType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $builder
            ->addStep(
                $builder->createStepGroup('a')
                    ->addStep('a1', TextType::class)
                    ->addStep('a2', TextType::class)
            )
            ->addStep('b', TextType::class)
            ->addStep(
                $builder->createStepGroup('c')
                    ->addStep('c1', TextType::class)
            );

        $builder->add('navigator', NavigatorFlowType::class, ['with_reset' => true]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'data_storage' => new InMemoryDataStorage('group_steps_flow'),
            'step_property_path' => '[currentStep]',
        ]);
    }
}
