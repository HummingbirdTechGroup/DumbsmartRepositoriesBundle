<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependecyInjection;

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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('orm')->defaultFalse()->end()
                        ->booleanNode('odm')->defaultFalse()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $rootNode;
    }
}
