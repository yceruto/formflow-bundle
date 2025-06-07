<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class TestAppKernel extends Kernel
{
    use MicroKernelTrait;

    private string $varDir;
    private string $testCase;
    private string $rootConfig;

    public function __construct($varDir, $testCase, $rootConfig, $environment, $debug)
    {
        if (!is_dir(__DIR__.'/'.$testCase)) {
            throw new \InvalidArgumentException(sprintf('The test case "%s" does not exist.', $testCase));
        }
        $this->varDir = $varDir;
        $this->testCase = $testCase;

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($rootConfig) && !file_exists($rootConfig = __DIR__.'/'.$testCase.'/'.$rootConfig)) {
            $rootConfig = __DIR__.'/config.yaml';
        }
        $this->rootConfig = $rootConfig;

        parent::__construct($environment, $debug);
    }

    public function registerBundles(): iterable
    {
        if (!file_exists($filename = $this->getProjectDir().'/'.$this->testCase.'/bundles.php')) {
            $filename = $this->getProjectDir().'/bundles.php';
        }

        return include $filename;
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/'.$this->varDir.'/'.$this->testCase.'/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/'.$this->varDir.'/'.$this->testCase.'/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->rootConfig);
    }

    public function __sleep(): array
    {
        return ['varDir', 'testCase', 'rootConfig', 'environment', 'debug'];
    }

    public function __wakeup(): void
    {
        $this->__construct($this->varDir, $this->testCase, $this->rootConfig, $this->environment, $this->debug);
    }

    protected function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.test_case'] = $this->testCase;

        return $parameters;
    }
}
