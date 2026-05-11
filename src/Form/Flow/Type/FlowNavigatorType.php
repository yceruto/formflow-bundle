<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A navigator type that defines default buttons to interact with a form flow.
 */
class FlowNavigatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('previous', FlowPreviousType::class);
        $builder->add('next', FlowNextType::class);
        $builder->add('finish', FlowFinishType::class);

        if ($options['with_reset']) {
            $builder->add('reset', FlowResetType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
            'priority' => -100,
        ]);

        $resolver->define('with_reset')
            ->allowedTypes('bool')
            ->default(false)
            ->info('Whether to add a reset button to restart the flow from the first step');
    }
}
