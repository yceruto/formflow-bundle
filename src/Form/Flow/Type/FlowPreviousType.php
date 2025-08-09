<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractFlowButtonType;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonInterface;
use Yceruto\FormFlowBundle\Form\Flow\FlowCursor;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;

class FlowPreviousType extends AbstractFlowButtonType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('action', 'previous');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'handler' => fn (mixed $data, FlowButtonInterface $button, FormFlowInterface $flow) => $flow->movePrevious($button->getViewData()),
            'include_if' => fn (FlowCursor $cursor): bool => $cursor->canMoveBack(),
            'clear_submission' => true,
        ]);
    }
}
