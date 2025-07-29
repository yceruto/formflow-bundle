<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\ButtonBuilder;

/**
 * A builder for {@link FlowButton} instances.
 */
class FlowButtonBuilder extends ButtonBuilder
{
    public function getForm(): FlowButton
    {
        return new FlowButton($this->getFormConfig());
    }
}
