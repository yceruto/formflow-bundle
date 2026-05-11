<?php

namespace Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Step;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSignUpProfessionalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('company');
        $builder->add('role', ChoiceType::class, [
            'choices' => [
                'Product Manager' => 'ROLE_MANAGER',
                'Developer' => 'ROLE_DEVELOPER',
                'Designer' => 'ROLE_DESIGNER',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'inherit_data' => true,
        ]);
    }
}
