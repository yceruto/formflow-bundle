<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Form\Data;

class MultistepDto
{
    // step 1
    public ?string $field11 = null;

    // step 2
    public ?string $field21 = null;
    public ?string $field22 = null;

    // step 3
    public ?string $field31 = null;
    public ?string $field32 = null;
    public ?string $field33 = null;

    // current step
    public ?string $step = null;
}
