<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\AbstractType;
use Yceruto\FormFlowBundle\Form\Flow\Type\FlowButtonType;

abstract class AbstractFlowButtonType extends AbstractType implements FlowButtonTypeInterface
{
    public function getParent(): string
    {
        return FlowButtonType::class;
    }
}
