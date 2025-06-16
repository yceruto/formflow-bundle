<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Yceruto\FormFlowBundle\Form\Extension\HttpFoundation\Type\FormFlowTypeSessionDataStorageExtension;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * @link https://symfony.com/doc/current/bundles/best_practices.html#services
 */
return static function (ContainerConfigurator $container): void {
    $container
        ->services()
            ->set('form.type_extension.form.flow.session_data_storage', FormFlowTypeSessionDataStorageExtension::class)
                ->args([service('request_stack')->ignoreOnInvalid()])
                ->tag('form.type_extension')
    ;
};
