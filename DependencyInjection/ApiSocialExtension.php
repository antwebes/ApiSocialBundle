<?php

namespace Ant\Bundle\ApiSocialBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ApiSocialExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('visits_limit', $config['visits_limit']);

        $parameters_service_dir = $config['parameters_service_yml']['file_dir'];
        $parameters_service_file = $config['parameters_service_yml']['file_name'];
        $parameters_service = $config['parameters_service'];


        if($parameters_service_dir == 'ant_api_social.config_dir'){
            $parameters_service_dir = $container->getParameter('ant_api_social.config_dir');
        }

        if($parameters_service != Configuration::PARAMETER_SERVICE_YML && $parameters_service !=  Configuration::PARAMETER_SERVICE_ANT ){
            throw new ParameterNotFoundException('parameters_service',$config['parameters_service']);
        }

        if(!file_exists($parameters_service_dir.DIRECTORY_SEPARATOR.$parameters_service_file)){
            throw new ParameterNotFoundException($parameters_service_dir,$parameters_service,'file_name');
        }

        $container->setParameter('ant_api_social.parameters_service',$config['parameters_service']);
        $container->setParameter('ant_api_social.parameters_service_dir',$parameters_service_dir);
        $container->setParameter('ant_api_social.parameters_service_file',$parameters_service_file);


        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if(count($config['users_orders']) == 0){
            $config['users_orders'] = null;
        }

        $container->setParameter('visits_limit', $config['visits_limit']);
        $container->setParameter('users_orders', $config['users_orders']);
        $container->setParameter('api_social.voyeur_limit', $config['voyeur_limit']);
        $container->setParameter('api_social.realtime_endpoint',$config['realtime_endpoint']);

    }
}
