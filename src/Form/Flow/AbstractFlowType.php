<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\AbstractType;
use Yceruto\FormFlowBundle\Form\Extension\Core\Type\FormFlowType;

abstract class AbstractFlowType extends AbstractType implements FormFlowTypeInterface
{
    public function getParent(): string
    {
        return FormFlowType::class;
    }
}
