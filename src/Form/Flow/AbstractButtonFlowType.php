<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\AbstractType;
use Yceruto\FormFlowBundle\Form\Flow\Type\ButtonFlowType;

abstract class AbstractButtonFlowType extends AbstractType implements ButtonFlowTypeInterface
{
    public function getParent(): string
    {
        return ButtonFlowType::class;
    }
}
