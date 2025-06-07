<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\ButtonBuilder;

/**
 * A builder for {@link ActionButton} instances.
 */
class ActionButtonBuilder extends ButtonBuilder
{
    public function getForm(): ActionButton
    {
        return new ActionButton($this->getFormConfig());
    }
}
