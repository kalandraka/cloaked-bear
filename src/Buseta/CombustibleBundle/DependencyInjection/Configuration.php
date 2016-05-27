<?php

namespace Buseta\CombustibleBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('buseta_combustible');

        $rootNode
            ->children()
            ->arrayNode('server')
            ->children()
                ->scalarNode('address')->end()
                ->scalarNode('port')->end()
                ->scalarNode('alias')->end()
            ->end()
            ->end()// server
            ->end();

        return $treeBuilder;
    }
}
