<?php

namespace Yceruto\FormFlowBundle\Form\Flow;

use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\FormInterface;

interface ActionButtonInterface extends FormInterface, ClickableInterface
{
    /**
     * Returns the action name configured for the button.
     */
    public function getAction(): string;

    /**
     * Returns the callable handler configured for the button.
     */
    public function getHandler(): callable;

    /**
     * Checks if the callable handler was already called.
     */
    public function isHandled(): bool;

    /**
     * Executes the callable handler.
     */
    public function handle(): void;

    /**
     * Checks if the button's action is 'reset'.
     */
    public function isResetAction(): bool;

    /**
     * Checks if the button's action is 'back'.
     */
    public function isBackAction(): bool;

    /**
     * Checks if the button's action is 'next'.
     */
    public function isNextAction(): bool;

    /**
     * Checks if the button's action is 'finish'.
     */
    public function isFinishAction(): bool;

    /**
     * Checks if the button is configured to clear submission data.
     */
    public function isClearSubmission(): bool;
}
