<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;

/**
 * A button that submits the form and handles an action.

 */
class FlowButton extends SubmitButton implements FlowButtonInterface
{
    private mixed $data = null;
    private bool $handled = false;

    public function submit(array|string|null $submittedData, bool $clearMissing = true): static
    {
        if ($this->isSubmitted()) {
            return $this; // ignore double submit
        }

        parent::submit($submittedData, $clearMissing);

        if ($this->isSubmitted()) {
            $this->data = $submittedData;
        }

        return $this;
    }

    public function getViewData(): mixed
    {
        return $this->data;
    }

    public function handle(): void
    {
        /** @var FormInterface $form */
        $form = $this->getParent();
        $data = $form->getData();

        while ($form && !$form instanceof FormFlowInterface) {
            $form = $form->getParent();
        }

        $handler = $this->getConfig()->getOption('handler');
        $handler($data, $this, $form);

        $this->handled = true;
    }

    public function isHandled(): bool
    {
        return $this->handled;
    }

    public function isResetAction(): bool
    {
        return 'reset' === $this->getConfig()->getAttribute('action');
    }

    public function isPreviousAction(): bool
    {
        return 'previous' === $this->getConfig()->getAttribute('action');
    }

    public function isNextAction(): bool
    {
        return 'next' === $this->getConfig()->getAttribute('action');
    }

    public function isFinishAction(): bool
    {
        return 'finish' === $this->getConfig()->getAttribute('action');
    }

    public function isClearSubmission(): bool
    {
        return $this->getConfig()->getOption('clear_submission');
    }
}
