<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\ButtonBuilder;

/**
 * A builder for {@link ButtonFlow} instances.
 */
class ButtonFlowBuilder extends ButtonBuilder
{
    public function getForm(): ButtonFlow
    {
        return new ButtonFlow($this->getFormConfig());
    }
}
