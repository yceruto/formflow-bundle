<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Twig\Extension\AbstractExtension;
use Yceruto\FormFlowBundle\Form\Extension\HttpFoundation\Type\FormFlowTypeSessionDataStorageExtension;
use Yceruto\FormFlowBundle\Twig\FormFlowExtension;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services
        ->set('form.type_extension.form.flow.session_data_storage', FormFlowTypeSessionDataStorageExtension::class)
            ->args([service('request_stack')->ignoreOnInvalid()])
            ->tag('form.type_extension')
    ;

    if (class_exists(AbstractExtension::class)) {
        $services
            ->set('form.flow.twig.extension', FormFlowExtension::class)
                ->tag('twig.extension')
        ;
    }
};
