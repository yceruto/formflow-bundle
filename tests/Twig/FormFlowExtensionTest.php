<?php

namespace Yceruto\FormFlowBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Yceruto\FormFlowBundle\Form\ResolvedFormTypeFactory;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Data\UserSignUp;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\NestedStepsFlowType;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\UserSignUpType;
use Yceruto\FormFlowBundle\Twig\FormFlowExtension;

class FormFlowExtensionTest extends TestCase
{
    private FormFactoryInterface $factory;
    private FormFlowExtension $extension;

    protected function setUp(): void
    {
        $this->factory = Forms::createFormFactoryBuilder()
            ->setResolvedTypeFactory(new ResolvedFormTypeFactory())
            ->getFormFactory();

        $this->extension = new FormFlowExtension();
    }

    public function testFlowAtFirstStep()
    {
        $view = $this->createFlowView('personal');

        self::assertSame(3, $this->extension->getFormFlowTotalSteps($view));
        self::assertSame(['personal', 'professional', 'account'], $this->extension->getFormFlowSteps($view));
        self::assertSame('personal', $this->extension->getFormFlowCurrentStep($view));
        self::assertSame(0, $this->extension->getFormFlowStepIndex($view));
        self::assertSame('professional', $this->extension->getFormFlowNextStep($view));
        self::assertNull($this->extension->getFormFlowPreviousStep($view));
        self::assertSame('personal', $this->extension->getFormFlowFirstStep($view));
        self::assertSame('account', $this->extension->getFormFlowLastStep($view));
        self::assertTrue($this->extension->isFormFlowFirstStep($view));
        self::assertFalse($this->extension->isFormFlowLastStep($view));
        self::assertTrue($this->extension->canFormFlowMoveNext($view));
        self::assertFalse($this->extension->canFormFlowMoveBack($view));
    }

    public function testFlowAtMiddleStep()
    {
        $view = $this->createFlowView('professional');

        self::assertSame(3, $this->extension->getFormFlowTotalSteps($view));
        self::assertSame(['personal', 'professional', 'account'], $this->extension->getFormFlowSteps($view));
        self::assertSame('professional', $this->extension->getFormFlowCurrentStep($view));
        self::assertSame(1, $this->extension->getFormFlowStepIndex($view));
        self::assertSame('account', $this->extension->getFormFlowNextStep($view));
        self::assertSame('personal', $this->extension->getFormFlowPreviousStep($view));
        self::assertSame('personal', $this->extension->getFormFlowFirstStep($view));
        self::assertSame('account', $this->extension->getFormFlowLastStep($view));
        self::assertFalse($this->extension->isFormFlowFirstStep($view));
        self::assertFalse($this->extension->isFormFlowLastStep($view));
        self::assertTrue($this->extension->canFormFlowMoveNext($view));
        self::assertTrue($this->extension->canFormFlowMoveBack($view));
    }

    public function testFlowAtLastStep()
    {
        $view = $this->createFlowView('account');

        self::assertSame(3, $this->extension->getFormFlowTotalSteps($view));
        self::assertSame(['personal', 'professional', 'account'], $this->extension->getFormFlowSteps($view));
        self::assertSame('account', $this->extension->getFormFlowCurrentStep($view));
        self::assertSame(2, $this->extension->getFormFlowStepIndex($view));
        self::assertNull($this->extension->getFormFlowNextStep($view));
        self::assertSame('professional', $this->extension->getFormFlowPreviousStep($view));
        self::assertSame('personal', $this->extension->getFormFlowFirstStep($view));
        self::assertSame('account', $this->extension->getFormFlowLastStep($view));
        self::assertFalse($this->extension->isFormFlowFirstStep($view));
        self::assertTrue($this->extension->isFormFlowLastStep($view));
        self::assertFalse($this->extension->canFormFlowMoveNext($view));
        self::assertTrue($this->extension->canFormFlowMoveBack($view));
    }

    public function testFormWithoutFlowThrows()
    {
        $view = $this->factory->create(FormType::class)->createView();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The "form_flow_*" functions can only be used on a form flow view');

        $this->extension->getFormFlowCurrentStep($view);
    }

    public function testRootSteps()
    {
        $view = $this->createNestedFlowView();

        self::assertSame(['stepA', 'stepB', 'stepC'], $this->extension->getFormFlowRootSteps($view));
    }

    public function testParentStep()
    {
        $view = $this->createNestedFlowView();

        // Defaults to the current step (stepA1, the first visitable step).
        self::assertSame('stepA', $this->extension->getFormFlowParentStep($view));
        self::assertSame('stepA', $this->extension->getFormFlowParentStep($view, 'stepA1'));
        self::assertSame('stepB1', $this->extension->getFormFlowParentStep($view, 'stepB11'));
        self::assertNull($this->extension->getFormFlowParentStep($view, 'stepC'));
    }

    public function testChildSteps()
    {
        $view = $this->createNestedFlowView();

        self::assertSame(['stepA1', 'stepA2', 'stepA3'], $this->extension->getFormFlowChildSteps($view, 'stepA'));
        self::assertSame(['stepB1', 'stepB2'], $this->extension->getFormFlowChildSteps($view, 'stepB'));
        self::assertSame(['stepB11', 'stepB12'], $this->extension->getFormFlowChildSteps($view, 'stepB1'));
        self::assertSame([], $this->extension->getFormFlowChildSteps($view, 'stepC'));
    }

    public function testAncestorSteps()
    {
        $view = $this->createNestedFlowView();

        self::assertSame(['stepA'], $this->extension->getFormFlowAncestorSteps($view, 'stepA1'));
        self::assertSame(['stepB', 'stepB1'], $this->extension->getFormFlowAncestorSteps($view, 'stepB11'));
        self::assertSame([], $this->extension->getFormFlowAncestorSteps($view, 'stepC'));
    }

    public function testStepDepth()
    {
        $view = $this->createNestedFlowView();

        self::assertSame(0, $this->extension->getFormFlowStepDepth($view, 'stepA'));
        self::assertSame(1, $this->extension->getFormFlowStepDepth($view, 'stepA1'));
        self::assertSame(1, $this->extension->getFormFlowStepDepth($view, 'stepB1'));
        self::assertSame(2, $this->extension->getFormFlowStepDepth($view, 'stepB11'));
        self::assertSame(0, $this->extension->getFormFlowStepDepth($view, 'stepC'));
    }

    public function testIsGroup()
    {
        $view = $this->createNestedFlowView();

        self::assertTrue($this->extension->isFormFlowGroup($view, 'stepA'));
        // stepB1 has children but was not created as a group.
        self::assertFalse($this->extension->isFormFlowGroup($view, 'stepB1'));
        self::assertFalse($this->extension->isFormFlowGroup($view, 'stepC'));
    }

    public function testStepInfo()
    {
        $view = $this->createNestedFlowView();

        $info = $this->extension->getFormFlowStepInfo($view, 'stepB1');

        self::assertSame('stepB1', $info['name']);
        self::assertSame(1, $info['level']);
        self::assertFalse($info['is_group']);
        self::assertFalse($info['is_skipped']);
        self::assertArrayHasKey('stepB11', $info['children']);
        self::assertArrayHasKey('stepB12', $info['children']);

        // The current step (stepA1) is flagged as such in the computed tree.
        self::assertTrue($this->extension->getFormFlowStepInfo($view, 'stepA1')['is_current_step']);

        // Defaults to the current step when no step name is given.
        self::assertSame('stepA1', $this->extension->getFormFlowStepInfo($view)['name']);
    }

    public function testStepInfoForUnknownStepThrows()
    {
        $view = $this->createNestedFlowView();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Step "unknown" does not exist in the flow view.');

        $this->extension->getFormFlowStepInfo($view, 'unknown');
    }

    public function testNestedHelpersOnFormWithoutFlowThrow()
    {
        $view = $this->factory->create(FormType::class)->createView();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The "form_flow_*" functions can only be used on a form flow view');

        $this->extension->getFormFlowRootSteps($view);
    }

    private function createFlowView(string $currentStep): FormView
    {
        $data = new UserSignUp();
        $data->worker = true;
        $data->currentStep = $currentStep;

        return $this->factory->create(UserSignUpType::class, $data)
            ->getStepForm()
            ->createView();
    }

    private function createNestedFlowView(): FormView
    {
        return $this->factory->create(NestedStepsFlowType::class, [])->createView();
    }
}
