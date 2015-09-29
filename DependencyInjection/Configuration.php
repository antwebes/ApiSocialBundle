<?php

namespace Ant\Bundle\ApiSocialBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * this constant define the load parameter from yml
     */
    const PARAMETER_SERVICE_YML = 'parameters_in_yml';
    /**
     * this constant define the load parameter from WebSiteParametersBundle
     */
    const PARAMETER_SERVICE_ANT = 'ant_web_site_parameters';

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('api_social');

        $rootNode->children()
            ->integerNode('visits_limit')
                ->defaultValue(3)
                ->end()
            ->enumNode('parameters_service')
                ->values(array(self::PARAMETER_SERVICE_YML, self::PARAMETER_SERVICE_ANT))
                    ->defaultValue(self::PARAMETER_SERVICE_ANT)
            ->end()
            ->arrayNode('parameters_service_yml')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('file_dir')->isRequired()->cannotBeEmpty()->defaultValue('ant_api_social.config_dir')->end()
                    ->scalarNode('file_name')->isRequired()->cannotBeEmpty()->defaultValue('parameters.yml')->end()
                ->end()
            ->end()
        ->end();

        $rootNode->children()
            ->arrayNode('users_orders')
                ->prototype('scalar')->end()
            ->end();

        $rootNode->children()
            ->integerNode('voyeur_limit')
            ->defaultValue(3)
            ->end();

        $rootNode->children()
            ->scalarNode('realtime_endpoint')
                ->defaultValue('http://127.0.0.1:8000')
            ->end()
        ;

        return $treeBuilder;
    }
}
