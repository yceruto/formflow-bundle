<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Extension\Core\Type\FormFlowNavigatorType;
use Yceruto\FormFlowBundle\Form\Flow\AbstractFlowType;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Form\Data\MultistepDto;

class MultistepType extends AbstractFlowType
{
    /**
     * @param FormFlowBuilderInterface $builder
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addStep('step1', Step1Type::class);
        $builder->addStep('step2', Step2Type::class);
        $builder->addStep('step3', Step3Type::class);

        $builder->add('navigator', FormFlowNavigatorType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MultistepDto::class,
            'step_property_path' => 'step'
        ]);
    }
}
