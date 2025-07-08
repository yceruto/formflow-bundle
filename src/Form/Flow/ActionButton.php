<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;

/**
 * A button that submits the form and handles an action.
 */
class ActionButton extends SubmitButton implements ActionButtonInterface
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

    public function getAction(): string
    {
        return $this->getConfig()->getOption('action');
    }

    public function getHandler(): callable
    {
        return $this->getConfig()->getOption('handler');
    }

    public function isHandled(): bool
    {
        return $this->handled;
    }

    public function handle(): void
    {
        /** @var FormInterface $form */
        $form = $this->getParent();
        $data = $form->getData();

        while ($form && !$form instanceof FormFlowInterface) {
            $form = $form->getParent();
        }

        $handler = $this->getHandler();
        $handler($data, $this, $form);

        $this->handled = true;
    }

    public function isResetAction(): bool
    {
        return 'reset' === $this->getAction();
    }

    public function isBackAction(): bool
    {
        return 'back' === $this->getAction();
    }

    public function isNextAction(): bool
    {
        return 'next' === $this->getAction();
    }

    public function isFinishAction(): bool
    {
        return 'finish' === $this->getAction();
    }

    public function isClearSubmission(): bool
    {
        return $this->getConfig()->getOption('clear_submission');
    }
}
