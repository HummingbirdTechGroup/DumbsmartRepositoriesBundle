<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dumbsmart_repositories');

        $rootNode
            ->children()
                ->arrayNode('autoconfigure')
                    ->children()
                        ->booleanNode('orm')->end()
                        ->booleanNode('odm')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
