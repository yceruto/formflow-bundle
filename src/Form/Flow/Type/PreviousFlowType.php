<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractButtonFlowType;
use Yceruto\FormFlowBundle\Form\Flow\ButtonFlowInterface;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowCursor;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;

class PreviousFlowType extends AbstractButtonFlowType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('action', 'previous');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'handler' => fn (mixed $data, ButtonFlowInterface $button, FormFlowInterface $flow) => $flow->movePrevious($button->getViewData()),
            'include_if' => fn (FormFlowCursor $cursor): bool => $cursor->canMoveBack(),
            'clear_submission' => true,
        ]);
    }
}
