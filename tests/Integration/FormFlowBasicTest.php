<?php

namespace Yceruto\FormFlowBundle\Tests\Integration;

use Symfony\Component\Form\Extension\DataCollector\Proxy\ResolvedTypeFactoryDataCollectorProxy;
use Yceruto\FormFlowBundle\Form\ResolvedFormTypeFactory;

class FormFlowBasicTest extends AbstractWebTestCase
{
    public function testFormFlow(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/basic');

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step1<', $crawler->html());
        self::assertSameFileContent('step1.html', $client->getInternalResponse()->getContent());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => 'foo',
            'multistep[navigator][next]' => '',
        ]);

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step2<', $crawler->html());
        self::assertSameFileContent('step2.html', $crawler->filter('body')->html());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step2][field21]' => 'foo',
            'multistep[step2][field22]' => 'bar',
            'multistep[navigator][next]' => '',
        ]);

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step3<', $crawler->html());
        self::assertSameFileContent('step3.html', $crawler->filter('body')->html());

        $client->submit($crawler->selectButton('Finish')->form(), [
            'multistep[step3][field31]' => 'foo',
            'multistep[step3][field32]' => 'bar',
            'multistep[step3][field33]' => 'baz',
            'multistep[navigator][finish]' => '',
        ]);

        self::assertSame(302, $client->getInternalResponse()->getStatusCode());
        self::assertSame('/basic/success', $client->getInternalResponse()->getHeader('Location'));
    }

    public function testGoBackToPreviousStep(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/basic');

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => 'foo',
            'multistep[navigator][next]' => '',
        ]);

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step2][field21]' => 'foo',
            'multistep[step2][field22]' => 'bar',
            'multistep[navigator][next]' => '',
        ]);

        self::assertStringContainsString('>Step3<', $crawler->html());

        $crawler = $client->submit($crawler->selectButton('Back')->form(), [
            'multistep[navigator][back]' => '',
        ]);

        self::assertStringContainsString('>Step2<', $crawler->html());
        self::assertStringContainsString('value="foo"', $crawler->html());
        self::assertStringContainsString('value="bar"', $crawler->html());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step2][field21]' => 'foo',
            'multistep[step2][field22]' => 'bar',
            'multistep[navigator][next]' => '',
        ]);

        $client->submit($crawler->selectButton('Finish')->form(), [
            'multistep[step3][field31]' => 'foo',
            'multistep[step3][field32]' => 'bar',
            'multistep[step3][field33]' => 'baz',
            'multistep[navigator][finish]' => '',
        ]);

        self::assertSame(302, $client->getInternalResponse()->getStatusCode());
        self::assertSame('/basic/success', $client->getInternalResponse()->getHeader('Location'));
    }

    public function testGoBackToEarlierStep(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/basic');

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => 'foo',
            'multistep[navigator][next]' => '',
        ]);

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step2][field21]' => 'foo',
            'multistep[step2][field22]' => 'bar',
            'multistep[navigator][next]' => '',
        ]);

        self::assertStringContainsString('>Step3<', $crawler->html());

        $crawler = $client->submit($crawler->selectButton('Back')->form(), [
            'multistep[navigator][back]' => 'step1',
        ]);

        self::assertStringContainsString('>Step1<', $crawler->html());
        self::assertStringContainsString('value="foo"', $crawler->html());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => 'foo',
            'multistep[navigator][next]' => '',
        ]);

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step2][field21]' => 'foo',
            'multistep[step2][field22]' => 'bar',
            'multistep[navigator][next]' => '',
        ]);

        $client->submit($crawler->selectButton('Finish')->form(), [
            'multistep[step3][field31]' => 'foo',
            'multistep[step3][field32]' => 'bar',
            'multistep[step3][field33]' => 'baz',
            'multistep[navigator][finish]' => '',
        ]);

        self::assertSame(302, $client->getInternalResponse()->getStatusCode());
        self::assertSame('/basic/success', $client->getInternalResponse()->getHeader('Location'));
    }

    public function testSkipStep(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/basic');

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => 'foo',
            'multistep[navigator][next]' => '',
        ]);

        self::assertStringContainsString('>Step2<', $crawler->html());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step2][field21]' => '',
            'multistep[step2][field22]' => '',
            'multistep[navigator][next]' => '',
        ]);

        self::assertSame(422, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step2<', $crawler->html());

        $crawler = $client->submit($crawler->selectButton('Skip')->form(), [
            'multistep[step2][skip]' => '',
        ]);

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step3<', $crawler->html());

        $crawler = $client->submit($crawler->selectButton('Back')->form(), [
            'multistep[navigator][back]' => '',
        ]);

        self::assertStringContainsString('>Step2<', $crawler->html());
        self::assertStringNotContainsString('value="foo"', $crawler->html());
        self::assertStringNotContainsString('value="bar"', $crawler->html());
    }

    public function testValidationError(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/basic');

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step1<', $crawler->html());
        self::assertSameFileContent('step1.html', $client->getInternalResponse()->getContent());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => '',
            'multistep[navigator][next]' => '',
        ]);

        self::assertSame(422, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step1<', $crawler->html());
        self::assertSameFileContent('step1_error.html', $crawler->filter('body')->html());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => 'foo',
            'multistep[navigator][next]' => '',
        ]);

        self::assertSame(200, $client->getInternalResponse()->getStatusCode());
        self::assertStringContainsString('>Step2<', $crawler->html());
        self::assertSameFileContent('step2.html', $crawler->filter('body')->html());
    }

    public function testResolvedTypeFactoryDataCollectorProxyWithProfiler(): void
    {
        $factory = self::getContainer()->get('form.resolved_type_factory');

        self::assertInstanceOf(ResolvedFormTypeFactory::class, $factory);
    }

    public function testResolvedFormTypeFactoryWithProfiler(): void
    {
        self::bootKernel([
            'var_dir' => 'formflow_with_profiler',
            'root_config' => __DIR__.'/App/config_with_profiler.yaml',
        ]);

        $factory = self::getContainer()->get('form.resolved_type_factory');

        self::assertInstanceOf(ResolvedTypeFactoryDataCollectorProxy::class, $factory);
    }

    private static function assertSameFileContent(string $expectedFilename, string $actualContent, bool $save = false): void
    {
        $expectedContent = self::getOutputFileContent($expectedFilename, $actualContent, $save);

        self::assertSame($expectedContent, $actualContent);
    }

    private static function getOutputFileContent(string $name, string $content, bool $save = false): string
    {
        $projectDir = static::$kernel->getContainer()->getParameter('kernel.project_dir');
        $testCase = static::$kernel->getContainer()->getParameter('kernel.test_case');
        $filename = $projectDir.'/'.$testCase.\sprintf('/Output/%s', $name);

        if ($save) {
            file_put_contents($filename, $content);
        }

        return file_get_contents($filename);
    }
}
