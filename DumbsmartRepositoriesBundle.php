<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DumbsmartRepositoriesBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $metadataConfigurer = $this->container->get('dumbsmart_repositories.metadata_configurer');
        $repositoryConfigurer = $this->container->get('dumbsmart_repositories.repository_configurer');

        if ($this->container->getParameter('dumbsmart_repositories.config.autoload.orm')) {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');

            $metadataConfigurer->configureMetadata($entityManager->getMetadataFactory());
            $repositoryConfigurer->configureRepositories($entityManager->getMetadataFactory());
        }

        if ($this->container->getParameter('dumbsmart_repositories.config.autoload.odm')) {
            $documentManager = $this->container->get('doctrine_mongodb.odm.document_manager');

            $metadataConfigurer->configureMetadata($documentManager->getMetadataFactory());
            $repositoryConfigurer->configureRepositories($documentManager->getMetadataFactory());
        }
    }
}
