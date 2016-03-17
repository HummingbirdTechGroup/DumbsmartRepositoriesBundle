<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependencyInjection;

use carlosV2\DumbsmartRepositoriesBundle\RepositoryFactories\InMemoryRepositoryFactory;
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
                ->arrayNode('repositories')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')
                            ->defaultValue(InMemoryRepositoryFactory::TYPE)
                        ->end()
                        ->scalarNode('path')
                            ->defaultValue(sys_get_temp_dir())
                            ->beforeNormalization()
                                ->always(function ($path) {
                                    return ($path ? realpath($path) : sys_get_temp_dir());
                                })
                            ->end()
                            ->validate()
                                ->ifTrue(function ($path) {
                                    return !is_dir($path) || !is_writable($path);
                                })
                                ->thenInvalid('Not a valid path or not writable')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('autoconfigure')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('orm')
                            ->defaultFalse()
                        ->end()
                        ->booleanNode('odm')
                            ->defaultFalse()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('aliases')
                    ->prototype('scalar')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
