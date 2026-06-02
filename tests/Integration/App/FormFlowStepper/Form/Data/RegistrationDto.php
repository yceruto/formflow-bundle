<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Form\Data;

use Symfony\Component\Validator\Constraints as Assert;

class RegistrationDto
{
    // account / credentials step
    #[Assert\NotBlank(groups: ['credentials'])]
    public ?string $username = null;

    // account / security step
    #[Assert\NotBlank(groups: ['security'])]
    public ?string $password = null;

    // profile step
    public ?string $bio = null;

    // current step
    public ?string $step = null;
}
