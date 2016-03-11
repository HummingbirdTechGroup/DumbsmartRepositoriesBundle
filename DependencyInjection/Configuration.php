<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependencyInjection;

use carlosV2\DumbsmartRepositoriesBundle\RepositoryConfigurer;
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
                        ->enumNode('type')
                            ->defaultValue(RepositoryConfigurer::TYPE_IN_MEMORY)
                            ->values([RepositoryConfigurer::TYPE_IN_MEMORY, RepositoryConfigurer::TYPE_FILE])
                        ->end()
                        ->scalarNode('path')
                            ->defaultValue(sys_get_temp_dir())
                            ->beforeNormalization()
                                ->always(function ($value) {
                                    return ($value ? realpath($value) : sys_get_temp_dir());
                                })
                            ->end()
                            ->validate()
                                ->ifTrue(function ($value) {
                                    return !is_dir($value);
                                })
                                ->thenInvalid('Not a valid path')
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
            ->end()
        ;

        return $treeBuilder;
    }
}
