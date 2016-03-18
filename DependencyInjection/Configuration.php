<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependencyInjection;

use carlosV2\DumbsmartRepositoriesBundle\Configurer\RepositoryFactory;
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
                            ->defaultValue(RepositoryFactory::TYPE_IN_MEMORY)
                            ->values([RepositoryFactory::TYPE_IN_MEMORY, RepositoryFactory::TYPE_FILE])
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
                    ->validate()
                        ->ifTrue(function (array $values) {
                            foreach ($values as $key => $value) {
                                if (!is_string($key) || !is_string($value)) {
                                    return true;
                                }
                            }

                            return false;
                        })
                        ->thenInvalid('Both keys and values must be strings')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
