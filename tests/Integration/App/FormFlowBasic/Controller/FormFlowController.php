<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Yceruto\FormFlowBundle\Form\Flow\FormFlowInterface;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Form\Data\MultistepDto;
use Yceruto\FormFlowBundle\Tests\Integration\App\FormFlowBasic\Form\Type\MultistepType;

class FormFlowController extends AbstractController
{
    #[Route('/basic', name: 'formflow_basic')]
    public function flow(Request $request): Response
    {
        /** @var FormFlowInterface $flow */
        $flow = $this->createForm(MultistepType::class, new MultistepDto());
        $flow->handleRequest($request);

        if ($flow->isSubmitted() && $flow->isValid() && $flow->isFinished()) {
            return new RedirectResponse('/basic/success');
        }

        return $this->render('flow.html.twig', [
            'form' => $flow->getStepForm(),
        ]);
    }
}
