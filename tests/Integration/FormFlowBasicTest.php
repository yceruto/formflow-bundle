<?php

namespace Yceruto\FormFlowBundle\Tests\Integration;

class FormFlowBasicTest extends AbstractWebTestCase
{
    public function testFormFlow(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/basic');

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('html');
        self::assertResponseStatusCodeSame(200);
        self::assertStringContainsString('>Step1<', $crawler->html());
        self::assertSameFileContent('step1.html', $client->getInternalResponse()->getContent());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step1][field11]' => 'foo',
            'multistep[navigator][next]' => '',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('html');
        self::assertResponseStatusCodeSame(200);
        self::assertStringContainsString('>Step2<', $crawler->html());
        self::assertSameFileContent('step2.html', $crawler->filter('body')->html());

        $crawler = $client->submit($crawler->selectButton('Next')->form(), [
            'multistep[step2][field21]' => 'foo',
            'multistep[step2][field22]' => 'bar',
            'multistep[navigator][next]' => '',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('html');
        self::assertResponseStatusCodeSame(200);
        self::assertStringContainsString('>Step3<', $crawler->html());
        self::assertSameFileContent('step3.html', $crawler->filter('body')->html());

        $client->submit($crawler->selectButton('Finish')->form(), [
            'multistep[step3][field31]' => 'foo',
            'multistep[step3][field32]' => 'bar',
            'multistep[step3][field33]' => 'baz',
            'multistep[navigator][finish]' => '',
        ]);

        self::assertResponseRedirects('/basic/success', 302);
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
