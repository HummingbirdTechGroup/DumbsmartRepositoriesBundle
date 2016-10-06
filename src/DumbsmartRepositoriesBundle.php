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
        $this->configureEntities();
        $this->configureDoctrine();
        $this->configureAliases();
    }

    private function configureEntities()
    {
        $configurer = $this->container->get('dumbsmart_repositories.entities_configurer');

        $configurer->configure($this->container->getParameter('dumbsmart_repositories.config.entities'));
    }

    private function configureDoctrine()
    {
        $configurer = $this->container->get('dumbsmart_repositories.doctrine_configurer');

        if ($this->container->getParameter('dumbsmart_repositories.config.autoload.orm')) {
            $entityManager = $this->container->get('doctrine.orm.entity_manager');
            $configurer->configure($entityManager->getMetadataFactory());
        }

        if ($this->container->getParameter('dumbsmart_repositories.config.autoload.odm')) {
            $documentManager = $this->container->get('doctrine_mongodb.odm.document_manager');
            $configurer->configure($documentManager->getMetadataFactory());
        }
    }

    private function configureAliases()
    {
        $configurer = $this->container->get('dumbsmart_repositories.aliases_configurer');

        $configurer->configure($this->container->getParameter('dumbsmart_repositories.config.aliases'));
    }
}
