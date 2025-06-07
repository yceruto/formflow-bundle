<?php

namespace Yceruto\FormFlowBundle\Form\Extension\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\ActionButtonInterface;
use Yceruto\FormFlowBundle\Form\Flow\ActionButtonTypeInterface;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowCursor;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;

/**
 * An action-based submit button for a form flow.
 */
class FormFlowActionType extends AbstractType implements ActionButtonTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('action')
            ->info('The action name of the button')
            ->default('')
            ->allowedTypes('string');

        $resolver->define('handler')
            ->info('A callable that will be called when this button is clicked')
            ->default(function (Options $options) {
                if (!\in_array($options['action'], ['back', 'next', 'finish', 'reset'], true)) {
                    throw new MissingOptionsException(\sprintf('The option "handler" is required for the action "%s".', $options['action']));
                }

                return function (mixed $data, ActionButtonInterface $button, FormFlowInterface $flow): void {
                    match (true) {
                        $button->isBackAction() => $flow->moveBack($button->getViewData()),
                        $button->isNextAction() => $flow->moveNext(),
                        $button->isFinishAction() => $flow->reset(),
                        $button->isResetAction() => $flow->reset(),
                    };
                };
            })
            ->allowedTypes('callable');

        $resolver->define('include_if')
            ->info('Decide whether to include this button in the current form')
            ->default(function (Options $options) {
                return match ($options['action']) {
                    'back' => fn (FormFlowCursor $cursor): bool => $cursor->canMoveBack(),
                    'next' => fn (FormFlowCursor $cursor): bool => $cursor->canMoveNext(),
                    'finish' => fn (FormFlowCursor $cursor): bool => $cursor->isLastStep(),
                    default => null,
                };
            })
            ->allowedTypes('null', 'array', 'callable')
            ->normalize(function (Options $options, mixed $value) {
                if (\is_array($value)) {
                    return fn (FormFlowCursor $cursor): bool => \in_array($cursor->getCurrentStep(), $value, true);
                }

                return $value;
            });

        $resolver->define('clear_submission')
            ->info('Whether the submitted data will be cleared when this button is clicked')
            ->default(function (Options $options) {
                return 'reset' === $options['action'] || 'back' === $options['action'];
            })
            ->allowedTypes('bool');

        $resolver->setDefault('validate', function (Options $options) {
            return !$options['clear_submission'];
        });

        $resolver->setDefault('validation_groups', function (Options $options) {
            return $options['clear_submission'] ? false : null;
        });
    }

    public function getParent(): string
    {
        return SubmitType::class;
    }
}
