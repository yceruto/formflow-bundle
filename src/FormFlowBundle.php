<?php

namespace Yceruto\FormFlowBundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Form\Extension\DataCollector\Proxy\ResolvedTypeFactoryDataCollectorProxy;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Yceruto\FormFlowBundle\Form\ResolvedFormTypeFactory;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html
 */
class FormFlowBundle extends AbstractBundle implements CompilerPassInterface
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass($this);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');
    }

    public function process(ContainerBuilder $container): void
    {
        $def = $container->getDefinition('form.resolved_type_factory');

        if (ResolvedTypeFactoryDataCollectorProxy::class === $def->getClass()) {
            $def->getArgument(0)->setClass(ResolvedFormTypeFactory::class);
        } else {
            $def->setClass(ResolvedFormTypeFactory::class);
        }
    }
}
