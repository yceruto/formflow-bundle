<?php

namespace Yceruto\FormFlowBundle\Tests\Integration\App;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class TestBundle extends Bundle implements CompilerPassInterface
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass($this);
    }

    public function shutdown(): void
    {
        restore_exception_handler();
    }

    public function process(ContainerBuilder $container): void
    {
        $container->getDefinition('form.resolved_type_factory')->setPublic(true);
    }
}
