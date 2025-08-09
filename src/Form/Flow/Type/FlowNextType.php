<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractFlowButtonType;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonInterface;
use Yceruto\FormFlowBundle\Form\Flow\FlowCursor;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;

class FlowNextType extends AbstractFlowButtonType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('action', 'next');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'handler' => fn (mixed $data, FlowButtonInterface $button, FormFlowInterface $flow) => $flow->moveNext(),
            'include_if' => fn (FlowCursor $cursor): bool => $cursor->canMoveNext(),
        ]);
    }
}
