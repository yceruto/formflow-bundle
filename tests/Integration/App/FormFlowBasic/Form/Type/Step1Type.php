<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Form\Data\MultistepDto;

class Step1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('field11');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MultistepDto::class,
            'inherit_data' => true,
        ]);
    }
}
