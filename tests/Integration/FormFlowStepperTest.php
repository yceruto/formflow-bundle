<?php

namespace Yceruto\FormFlowBundle\Tests\Integration;

/**
 * End-to-end test of the form_flow_* Twig helpers: drives a nested flow through the kernel
 * and asserts the stepper/breadcrumb rendered by tests/Integration/App/Templates/stepper.html.twig.
 */
class FormFlowStepperTest extends AbstractWebTestCase
{
    public function testStepperRendersNestedTreeAndAdvances(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/stepper');

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());

        // The "account" group renders as a group node containing the visitable steps.
        $stepper = $crawler->filter('.stepper')->html();
        self::assertStringContainsString('class="step is-group" data-level="0">account', $stepper);
        self::assertStringContainsString('class="step is-current" data-level="1">credentials', $stepper);
        self::assertStringContainsString('data-level="0">profile', $stepper);

        // Breadcrumb of the nested current step: account / credentials.
        $breadcrumb = $crawler->filter('.breadcrumb')->html();
        self::assertStringContainsString('<span class="ancestor">account</span>', $breadcrumb);
        self::assertStringContainsString('<span class="current">credentials</span>', $breadcrumb);

        // Advance to the second child of the group.
        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'registration[credentials][username]' => 'john',
            'registration[navigator][next]' => '',
        ]);

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('class="step is-current" data-level="1">security', $crawler->filter('.stepper')->html());
        self::assertStringContainsString('<span class="ancestor">account</span>', $crawler->filter('.breadcrumb')->html());
        self::assertStringContainsString('<span class="current">security</span>', $crawler->filter('.breadcrumb')->html());

        // Advance to the top-level profile step: no ancestors in the breadcrumb.
        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'registration[security][password]' => 'secret',
            'registration[navigator][next]' => '',
        ]);

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('class="step is-current" data-level="0">profile', $crawler->filter('.stepper')->html());

        $breadcrumb = $crawler->filter('.breadcrumb')->html();
        self::assertStringNotContainsString('class="ancestor"', $breadcrumb);
        self::assertStringContainsString('<span class="current">profile</span>', $breadcrumb);

        // Finishing on the last step redirects.
        $client->submit($crawler->selectButton('Finish')->form(), [
            'registration[profile][bio]' => 'hello',
            'registration[navigator][finish]' => '',
        ]);

        self::assertSame(302, $client->getInternalResponse()->getStatusCode());
        self::assertSame('/stepper/success', $client->getInternalResponse()->getHeader('Location'));
    }

    public function testStepperBacktracksAcrossGroupBoundary(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/stepper');

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'registration[credentials][username]' => 'john',
            'registration[navigator][next]' => '',
        ]);

        self::assertStringContainsString('<span class="current">security</span>', $crawler->filter('.breadcrumb')->html());

        // Go back to the first child of the group.
        $crawler = $client->submit($crawler->selectButton('Previous')->form(), [
            'registration[navigator][previous]' => '',
        ]);

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('<span class="current">credentials</span>', $crawler->filter('.breadcrumb')->html());
        self::assertStringContainsString('value="john"', $crawler->html());
    }
}
