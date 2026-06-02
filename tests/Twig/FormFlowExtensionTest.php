<?php

namespace Yceruto\FormFlowBundle\Tests\Twig;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;
use Yceruto\FormFlowBundle\Form\ResolvedFormTypeFactory;
use Yceruto\FormFlowBundle\Tests\Fixtures\Flow\Data\UserSignUp;
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

    private function createFlowView(string $currentStep): \Symfony\Component\Form\FormView
    {
        $data = new UserSignUp();
        $data->worker = true;
        $data->currentStep = $currentStep;

        return $this->factory->create(UserSignUpType::class, $data)
            ->getStepForm()
            ->createView();
    }
}
