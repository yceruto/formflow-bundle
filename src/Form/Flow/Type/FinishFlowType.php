<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractButtonFlowType;
use Yceruto\FormFlowBundle\Form\Flow\ButtonFlowInterface;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowCursor;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;

class FinishFlowType extends AbstractButtonFlowType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('action', 'finish');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'handler' => fn (mixed $data, ButtonFlowInterface $button, FormFlowInterface $flow) => $flow->reset(),
            'include_if' => fn (FormFlowCursor $cursor): bool => $cursor->isLastStep(),
        ]);
    }
}
