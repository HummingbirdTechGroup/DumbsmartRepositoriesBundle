<?php

namespace carlosV2\DumbsmartRepositoriesBundle\DependecyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class DumbsmartRepositoriesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        if ($configs['autoconfigure']['orm']) {
            $this->addAutomaticConfigurationForOrm($container);
        }

        if ($configs['autoconfigure']['odm']) {
            $this->addAutomaticConfigurationForOdm($container);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addAutomaticConfigurationForOrm(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine.orm.entity_manager')) {
            return;
        }
        $entityManagerDefinition = $container->getDefinition('doctrine.orm.entity_manager');

        $classMetadataFactory = new Definition('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory', []);
        $classMetadataFactory->setFactory([$entityManagerDefinition, 'getMetadataFactory']);
        $container->setDefinition('doctrine.orm.class_metadata_factory', $classMetadataFactory);

        $configurer = $container->getDefinition('dumbsmart_repositories.configurer');
        $configurer->addMethodCall('configure', [new Reference('doctrine.orm.class_metadata_factory')]);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addAutomaticConfigurationForOdm(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('doctrine_mongodb.odm.document_manager')) {
            return;
        }
        $documentManagerDefinition = $container->getDefinition('doctrine_mongodb.odm.document_manager');

        $classMetadataFactory = new Definition('Doctrine\Common\Persistence\Mapping\ClassMetadataFactory', []);
        $classMetadataFactory->setFactory([$documentManagerDefinition, 'getMetadataFactory']);
        $container->setDefinition('doctrine_mongodb.odm.class_metadata_factory', $classMetadataFactory);

        $configurer = $container->getDefinition('dumbsmart_repositories.configurer');
        $configurer->addMethodCall('configure', [new Reference('doctrine_mongodb.odm.class_metadata_factory')]);
    }
}
