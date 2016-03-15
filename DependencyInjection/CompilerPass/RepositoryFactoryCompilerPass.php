<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RepositoryFactoryCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $repositoryConfigurerDefinition = $container->getDefinition('dumbsmart_repositories.repository_configurer');

        foreach ($container->findTaggedServiceIds('dumbsmart_repositories.repository_factory') as $id => $tags) {
            $repositoryConfigurerDefinition->addMethodCall(
                'addRepositoryFactory',
                [new Reference($id)]
            );
        }
    }
}
