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
        $configurer = $this->container->get('dumbsmart_repositories.configurer');

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
}
