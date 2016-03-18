<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositoriesBundle\Configurer\MetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\ObjectIdentifierFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\RepositoryFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DumbsmartRepositoriesBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $configurer = $this->buildConfigurer();

        if ($this->container->getParameter('dumbsmart_repositories.config.autoload.orm')) {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');
            $configurer->configureEntities($entityManager->getMetadataFactory());
        }

        if ($this->container->getParameter('dumbsmart_repositories.config.autoload.odm')) {
            $documentManager = $this->container->get('doctrine_mongodb.odm.document_manager');
            $configurer->configureEntities($documentManager->getMetadataFactory());
        }

        $configurer->configureAliases($this->container->getParameter('dumbsmart_repositories.config.aliases'));
    }

    /**
     * @return Configurer
     */
    private function buildConfigurer()
    {
        return new Configurer(
            $this->container->get('dumbsmart_repositories.metadata_manager'),
            $this->container->get('dumbsmart_repositories.repository_manager'),
            $this->buildObjectIdentifierFactory(),
            $this->buildMetadataFactory(),
            $this->buildRepositoryFactory()
        );
    }

    /**
     * @return ObjectIdentifierFactory
     */
    private function buildObjectIdentifierFactory()
    {
        $objectIdentifierFactoryClass = $this->container->getParameter('dumbsmart_repositories.object_identifier_factory.class');
        return new $objectIdentifierFactoryClass();
    }

    /**
     * @return MetadataFactory
     */
    private function buildMetadataFactory()
    {
        $metadataFactoryClass = $this->container->getParameter('dumbsmart_repositories.metadata_factory.class');
        return new $metadataFactoryClass();
    }

    /**
     * @return RepositoryFactory
     */
    private function buildRepositoryFactory()
    {
        $type = $this->container->getParameter('dumbsmart_repositories.config.repositories.type');
        $path = $this->container->getParameter('dumbsmart_repositories.config.repositories.path');

        $repositoryFactoryClass = $this->container->getParameter('dumbsmart_repositories.repository_factory.class');
        return new $repositoryFactoryClass($type, $path);
    }
}
