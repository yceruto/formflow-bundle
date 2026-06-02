<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Form\Data\RegistrationDto;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('bio');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegistrationDto::class,
            'inherit_data' => true,
        ]);
    }
}
