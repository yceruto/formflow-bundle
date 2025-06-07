<?php

namespace Yceruto\FormFlowBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A navigator type that defines default actions to interact with a form flow.
 */
class FormFlowNavigatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('back', FormFlowActionType::class, [
            'action' => 'back',
        ]);

        $builder->add('next', FormFlowActionType::class, [
            'action' => 'next',
        ]);

        $builder->add('finish', FormFlowActionType::class, [
            'action' => 'finish',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
            'priority' => -100,
        ]);
    }
}
