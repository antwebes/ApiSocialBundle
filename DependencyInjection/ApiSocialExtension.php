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
