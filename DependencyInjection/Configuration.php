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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('api_social');

        $rootNode->children()
            ->integerNode('visits_limit')
            ->defaultValue(3)
            ->end();

        $rootNode->children()
            ->integerNode('minimum_votes_for_popular_photos')
            ->defaultValue(3)
            ->end();

        $rootNode->children()
            ->arrayNode('users_orders')
                ->prototype('scalar')->end()
            ->end();

        $rootNode->children()
            ->arrayNode('channels_orders')
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

        $rootNode->children()
            ->scalarNode('api_endpoint')->end()
        ;

        return $treeBuilder;
    }
}
