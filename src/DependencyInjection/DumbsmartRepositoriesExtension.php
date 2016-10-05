<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class DumbsmartRepositoriesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter('dumbsmart_repositories.config.autoload.orm', $config['autoconfigure']['orm']);
        $container->setParameter('dumbsmart_repositories.config.autoload.odm', $config['autoconfigure']['odm']);
        $container->setParameter('dumbsmart_repositories.config.repositories.type', $config['repositories']['type']);
        $container->setParameter('dumbsmart_repositories.config.repositories.path', $config['repositories']['path']);
        $container->setParameter('dumbsmart_repositories.config.aliases', $config['aliases']);
        $container->setParameter('dumbsmart_repositories.config.entities', $config['entities']);
    }
}
