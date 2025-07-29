<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonInterface;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonTypeInterface;
use Yceruto\FormFlowBundle\Form\Flow\FlowCursor;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;

class FlowFinishType extends AbstractType implements FlowButtonTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('action', 'finish');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'handler' => fn (mixed $data, FlowButtonInterface $button, FormFlowInterface $flow) => $flow->reset(),
            'include_if' => fn (FlowCursor $cursor): bool => $cursor->isLastStep(),
        ]);
    }

    public function getParent(): string
    {
        return FlowButtonType::class;
    }
}
