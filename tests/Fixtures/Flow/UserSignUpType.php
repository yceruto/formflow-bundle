<?php

namespace Yceruto\FormFlowBundle\Tests\Fixtures\Flow;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\AbstractFlowType;
use Yceruto\FormFlowBundle\Form\Flow\DataStorage\InMemoryDataStorage;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowBuilderInterface;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Data\UserSignUp;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Step\UserSignUpAccountType;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Step\UserSignUpPersonalType;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Step\UserSignUpProfessionalType;

class UserSignUpType extends AbstractFlowType
{
    public function buildFormFlow(FormFlowBuilderInterface $builder, array $options): void
    {
        $skip = $options['data_class']
            ? static fn (UserSignUp $data) => !$data->worker
            : static fn (array $data) => !$data['worker'];

        $builder->addStep('personal', UserSignUpPersonalType::class);
        $builder->addStep('professional', UserSignUpProfessionalType::class, [], $skip);
        $builder->addStep('account', UserSignUpAccountType::class);

        $builder->add('navigator', UserSignUpNavigatorType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSignUp::class,
            'data_storage' => new InMemoryDataStorage('user_sign_up'),
            'step_property_path' => 'currentStep',
        ]);
    }
}
