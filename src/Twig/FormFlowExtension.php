<?php

namespace Yceruto\FormFlowBundle\Twig;

use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowCursor;

/**
 * Twig helper functions to access values from the form flow view (the cursor and the steps tree).
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
            new TwigFunction('form_flow_root_steps', $this->getFormFlowRootSteps(...)),
            new TwigFunction('form_flow_parent_step', $this->getFormFlowParentStep(...)),
            new TwigFunction('form_flow_child_steps', $this->getFormFlowChildSteps(...)),
            new TwigFunction('form_flow_ancestor_steps', $this->getFormFlowAncestorSteps(...)),
            new TwigFunction('form_flow_step_depth', $this->getFormFlowStepDepth(...)),
            new TwigFunction('form_flow_is_group', $this->isFormFlowGroup(...)),
            new TwigFunction('form_flow_step_info', $this->getFormFlowStepInfo(...)),
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

    /**
     * Returns the names of the top-level steps of the flow tree.
     *
     * @return list<string>
     */
    public function getFormFlowRootSteps(FormView $view): array
    {
        return array_keys($this->getStepsTree($view));
    }

    /**
     * Returns the name of the parent step, or null if the given (or current) step is at the top level.
     */
    public function getFormFlowParentStep(FormView $view, ?string $step = null): ?string
    {
        $ancestors = $this->locateStep($view, $step)['ancestors'];

        return [] === $ancestors ? null : $ancestors[\count($ancestors) - 1];
    }

    /**
     * Returns the names of the direct child steps of the given (or current) step.
     *
     * @return list<string>
     */
    public function getFormFlowChildSteps(FormView $view, ?string $step = null): array
    {
        return array_keys($this->locateStep($view, $step)['info']['children']);
    }

    /**
     * Returns the ancestor step names of the given (or current) step, ordered from the root down to the direct parent.
     *
     * Useful to render a breadcrumb of the nested steps.
     *
     * @return list<string>
     */
    public function getFormFlowAncestorSteps(FormView $view, ?string $step = null): array
    {
        return $this->locateStep($view, $step)['ancestors'];
    }

    /**
     * Returns the nesting depth of the given (or current) step (0 for a top-level step).
     */
    public function getFormFlowStepDepth(FormView $view, ?string $step = null): int
    {
        return $this->locateStep($view, $step)['info']['level'];
    }

    /**
     * Returns whether the given (or current) step is a group, i.e. a non-visitable container of child steps.
     */
    public function isFormFlowGroup(FormView $view, ?string $step = null): bool
    {
        return $this->locateStep($view, $step)['info']['is_group'];
    }

    /**
     * Returns the computed view metadata of the given (or current) step from the nested steps tree.
     *
     * The returned array matches the per-step variables exposed by FormFlowType, e.g.
     * `name`, `level`, `index`, `position`, `is_current_step`, `is_before_current_step`,
     * `is_after_current_step`, `has_current_step_descendant`, `can_be_skipped`, `is_skipped`,
     * `is_group`, `children` and `visible_children`.
     *
     * @return array<string, mixed>
     */
    public function getFormFlowStepInfo(FormView $view, ?string $step = null): array
    {
        return $this->locateStep($view, $step)['info'];
    }

    private function getCursor(FormView $view): FormFlowCursor
    {
        $cursor = $view->vars['cursor'] ?? null;

        if (!$cursor instanceof FormFlowCursor) {
            throw new LogicException('The "form_flow_*" functions can only be used on a form flow view; none was found in the given view.');
        }

        return $cursor;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getStepsTree(FormView $view): array
    {
        $steps = $view->vars['steps'] ?? null;

        if (!\is_array($steps)) {
            throw new LogicException('The "form_flow_*" functions can only be used on a form flow view; none was found in the given view.');
        }

        return $steps;
    }

    /**
     * Locates a step (defaulting to the current step) within the nested steps tree.
     *
     * @return array{info: array<string, mixed>, ancestors: list<string>}
     */
    private function locateStep(FormView $view, ?string $step): array
    {
        $steps = $this->getStepsTree($view);
        $step ??= $this->findCurrentStepName($steps) ?? throw new LogicException('No current step found in the flow view.');

        return $this->searchStep($steps, $step) ?? throw new InvalidArgumentException(\sprintf('Step "%s" does not exist in the flow view.', $step));
    }

    /**
     * Recursively looks up a step's computed metadata and ancestor trail (root down to the direct parent) by name.
     *
     * @param array<string, array<string, mixed>> $steps
     * @param list<string>                         $ancestors
     *
     * @return array{info: array<string, mixed>, ancestors: list<string>}|null
     */
    private function searchStep(array $steps, string $name, array $ancestors = []): ?array
    {
        foreach ($steps as $stepName => $info) {
            if ($stepName === $name) {
                return ['info' => $info, 'ancestors' => $ancestors];
            }

            if ([] !== $info['children'] && null !== $found = $this->searchStep($info['children'], $name, [...$ancestors, $stepName])) {
                return $found;
            }
        }

        return null;
    }

    /**
     * Recursively finds the name of the current step within the nested steps tree.
     *
     * @param array<string, array<string, mixed>> $steps
     */
    private function findCurrentStepName(array $steps): ?string
    {
        foreach ($steps as $name => $info) {
            if ($info['is_current_step']) {
                return $name;
            }

            if ([] !== $info['children'] && null !== $found = $this->findCurrentStepName($info['children'])) {
                return $found;
            }
        }

        return null;
    }
}
