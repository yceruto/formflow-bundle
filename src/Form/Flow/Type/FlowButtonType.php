<?php

namespace Yceruto\FormFlowBundle\Form\Flow\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Yceruto\FormFlowBundle\Form\Flow\FlowButtonTypeInterface;
use Yceruto\FormFlowBundle\Form\Flow\FlowCursor;

/**
 * A submit button with a callable handler for a form flow.
 */
class FlowButtonType extends AbstractType implements FlowButtonTypeInterface
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('handler')
            ->info('The callable that will be called when this button is clicked')
            ->required()
            ->allowedTypes('callable');

        $resolver->define('include_if')
            ->info('Decide whether to include this button in the current form')
            ->default(null)
            ->allowedTypes('null', 'array', 'callable')
            ->normalize(function (Options $options, mixed $value) {
                if (\is_array($value)) {
                    return fn (FlowCursor $cursor): bool => \in_array($cursor->getCurrentStep(), $value, true);
                }

                return $value;
            });

        $resolver->define('clear_submission')
            ->info('Whether the submitted data will be cleared when this button is clicked')
            ->default(false)
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
