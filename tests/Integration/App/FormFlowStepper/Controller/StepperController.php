<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Form\Data\RegistrationDto;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowStepper\Form\Type\RegistrationType;

class StepperController extends AbstractController
{
    #[Route('/stepper', name: 'formflow_stepper')]
    public function flow(Request $request): Response
    {
        /** @var FormFlowInterface $flow */
        $flow = $this->createForm(RegistrationType::class, new RegistrationDto());
        $flow->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            return new RedirectResponse('/stepper/success');
        }

        return $this->render('stepper.html.twig', [
            'form' => $flow->getStepForm(),
        ]);
    }
}
