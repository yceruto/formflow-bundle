<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonInterface;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonTypeInterface;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;

class FlowResetType extends AbstractType implements FlowButtonTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('action', 'reset');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'handler' => fn (mixed $data, FlowButtonInterface $button, FormFlowInterface $flow) => $flow->reset(),
            'clear_submission' => true,
        ]);
    }

    public function getParent(): string
    {
        return FlowButtonType::class;
    }
}
