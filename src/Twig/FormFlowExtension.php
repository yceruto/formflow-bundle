<?php

namespace Yceruto\FormFlowBundle\Twig;

use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowCursor;

/**
 * Twig helper functions to access values from the form flow cursor.
 */
final class FormFlowExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('form_flow_total_steps', $this->getFormFlowTotalSteps(...)),
            new TwigFunction('form_flow_steps', $this->getFormFlowSteps(...)),
            new TwigFunction('form_flow_step_index', $this->getFormFlowStepIndex(...)),
            new TwigFunction('form_flow_current_step', $this->getFormFlowCurrentStep(...)),
            new TwigFunction('form_flow_next_step', $this->getFormFlowNextStep(...)),
            new TwigFunction('form_flow_previous_step', $this->getFormFlowPreviousStep(...)),
            new TwigFunction('form_flow_first_step', $this->getFormFlowFirstStep(...)),
            new TwigFunction('form_flow_last_step', $this->getFormFlowLastStep(...)),
            new TwigFunction('form_flow_is_first_step', $this->isFormFlowFirstStep(...)),
            new TwigFunction('form_flow_is_last_step', $this->isFormFlowLastStep(...)),
            new TwigFunction('form_flow_can_move_next', $this->canFormFlowMoveNext(...)),
            new TwigFunction('form_flow_can_move_back', $this->canFormFlowMoveBack(...)),
        ];
    }

    public function getFormFlowTotalSteps(FormView $view): int
    {
        return $this->getCursor($view)->getTotalSteps();
    }

    /**
     * @return list<string>
     */
    public function getFormFlowSteps(FormView $view): array
    {
        return $this->getCursor($view)->getSteps();
    }

    public function getFormFlowCurrentStep(FormView $view): string
    {
        return $this->getCursor($view)->getCurrentStep();
    }

    public function getFormFlowStepIndex(FormView $view): int
    {
        return $this->getCursor($view)->getStepIndex();
    }

    public function getFormFlowNextStep(FormView $view): ?string
    {
        return $this->getCursor($view)->getNextStep();
    }

    public function getFormFlowPreviousStep(FormView $view): ?string
    {
        return $this->getCursor($view)->getPreviousStep();
    }

    public function getFormFlowFirstStep(FormView $view): string
    {
        return $this->getCursor($view)->getFirstStep();
    }

    public function getFormFlowLastStep(FormView $view): string
    {
        return $this->getCursor($view)->getLastStep();
    }

    public function isFormFlowFirstStep(FormView $view): bool
    {
        return $this->getCursor($view)->isFirstStep();
    }

    public function isFormFlowLastStep(FormView $view): bool
    {
        return $this->getCursor($view)->isLastStep();
    }

    public function canFormFlowMoveBack(FormView $view): bool
    {
        return $this->getCursor($view)->canMoveBack();
    }

    public function canFormFlowMoveNext(FormView $view): bool
    {
        return $this->getCursor($view)->canMoveNext();
    }

    private function getCursor(FormView $view): FormFlowCursor
    {
        $cursor = $view->vars['cursor'] ?? null;

        if (!$cursor instanceof FormFlowCursor) {
            throw new LogicException('The "form_flow_*" functions can only be used on a form flow view; none was found in the given view.');
        }

        return $cursor;
    }
}
