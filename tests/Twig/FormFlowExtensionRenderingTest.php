<?php

namespace Yceruto\FormFlowBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Yceruto\FormFlowBundle\Form\ResolvedFormTypeFactory;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Data\UserSignUp;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\NestedStepsFlowType;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\UserSignUpType;
use Yceruto\FormFlowBundle\Twig\FormFlowExtension;

/**
 * Exercises the form_flow_* functions through a real Twig environment to ensure they are
 * registered and usable in templates, and to document the recommended rendering approaches.
 */
class FormFlowExtensionRenderingTest extends TestCase
{
    private FormFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = Forms::createFormFactoryBuilder()
            ->setResolvedTypeFactory(new ResolvedFormTypeFactory())
            ->getFormFactory();
    }

    public function testFlatProgressIndicator()
    {
        $view = $this->createFlatFlowView('professional');

        $output = $this->render('Step {{ form_flow_step_index(form) + 1 }} of {{ form_flow_total_steps(form) }}', $view);

        self::assertSame('Step 2 of 3', $output);
    }

    public function testFlatStepListWithCurrentMarker()
    {
        $view = $this->createFlatFlowView('professional');

        $template = '{% for step in form_flow_steps(form) %}{{ step }}'
            .'{{ step == form_flow_current_step(form) ? "*" : "" }}{{ not loop.last ? " " : "" }}{% endfor %}';

        self::assertSame('personal professional* account', $this->render($template, $view));
    }

    /**
     * Navigation buttons are best driven by the movement helpers (can_move_back / can_move_next).
     */
    public function testNavigationButtonsViaHelpers()
    {
        $template = '{{ form_flow_can_move_back(form) ? "back " : "" }}{{ form_flow_can_move_next(form) ? "next" : "finish" }}';

        self::assertSame('next', $this->render($template, $this->createFlatFlowView('personal')));
        self::assertSame('back next', $this->render($template, $this->createFlatFlowView('professional')));
        self::assertSame('back finish', $this->render($template, $this->createFlatFlowView('account')));
    }

    /**
     * Breadcrumbs of a nested step are best built with the ancestor helper.
     */
    public function testNestedBreadcrumbViaAncestorHelper()
    {
        $view = $this->createNestedFlowView('stepB11');

        $template = '{% for step in form_flow_ancestor_steps(form) %}{{ step }} / {% endfor %}{{ form_flow_current_step(form) }}';

        self::assertSame('stepB / stepB1 / stepB11', $this->render($template, $view));
    }

    /**
     * RECOMMENDED for full nested navigation: recurse over the `steps` view variable with a macro.
     * A single recursive walk renders the whole hierarchy with per-step state already attached.
     */
    public function testNestedTreeViaStepsVarRecursion()
    {
        $view = $this->createNestedFlowView('stepB11');

        $template = '{% macro branch(steps) %}{% import _self as m %}'
            .'{% for name, step in steps %}{{ name }}{{ step.is_current_step ? "*" : "" }}'
            .'{% if step.children is not empty %}({{ m.branch(step.children) }}){% endif %}'
            .'{{ not loop.last ? " " : "" }}{% endfor %}{% endmacro %}'
            .'{% import _self as m %}{{ m.branch(form.vars.steps) }}';

        self::assertSame(
            'stepA(stepA1 stepA2 stepA3) stepB(stepB1(stepB11* stepB12) stepB2) stepC',
            $this->render($template, $view),
        );
    }

    /**
     * The same nested tree can be rebuilt purely from the helpers (root_steps + child_steps),
     * but it requires repeated name-based lookups; this proves equivalence with the steps-var walk.
     */
    public function testNestedTreeViaHelpersIsEquivalent()
    {
        $view = $this->createNestedFlowView('stepB11');

        $template = '{% macro branch(form, names) %}{% import _self as m %}'
            .'{% for name in names %}{{ name }}{{ name == form_flow_current_step(form) ? "*" : "" }}'
            .'{% set children = form_flow_child_steps(form, name) %}'
            .'{% if children is not empty %}({{ m.branch(form, children) }}){% endif %}'
            .'{{ not loop.last ? " " : "" }}{% endfor %}{% endmacro %}'
            .'{% import _self as m %}{{ m.branch(form, form_flow_root_steps(form)) }}';

        self::assertSame(
            'stepA(stepA1 stepA2 stepA3) stepB(stepB1(stepB11* stepB12) stepB2) stepC',
            $this->render($template, $view),
        );
    }

    /**
     * Group headers vs. visitable steps can be told apart with form_flow_is_group()
     * and the per-step metadata from form_flow_step_info().
     */
    public function testGroupDetectionAndStepInfo()
    {
        $view = $this->createNestedFlowView('stepA1');

        $template = '{{ form_flow_is_group(form, "stepA") ? "group" : "step" }}:{{ form_flow_step_info(form, "stepA").level }}'
            .';{{ form_flow_is_group(form, "stepC") ? "group" : "step" }}:{{ form_flow_step_info(form, "stepC").level }}'
            .';depth-b11={{ form_flow_step_depth(form, "stepB11") }}';

        self::assertSame('group:0;step:0;depth-b11=2', $this->render($template, $view));
    }

    private function render(string $template, FormView $view): string
    {
        $twig = new Environment(new ArrayLoader(['flow' => $template]));
        $twig->addExtension(new FormFlowExtension());

        return $twig->render('flow', ['form' => $view]);
    }

    private function createFlatFlowView(string $currentStep): FormView
    {
        $data = new UserSignUp();
        $data->worker = true;
        $data->currentStep = $currentStep;

        return $this->factory->create(UserSignUpType::class, $data)
            ->getStepForm()
            ->createView();
    }

    private function createNestedFlowView(string $currentStep): FormView
    {
        return $this->factory->create(NestedStepsFlowType::class, ['currentStep' => $currentStep])->createView();
    }
}
