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
            if ($this->container->has('doctrine.orm.entity_manager')) {
                $entityManager = $this->container->get('doctrine.orm.entity_manager');
                $configurer->configure($entityManager->getMetadataFactory());
            }
        }

        if ($this->container->getParameter('dumbsmart_repositories.config.autoload.odm')) {
            if ($this->container->has('doctrine_mongodb.odm.document_manager')) {
                $entityManager = $this->container->get('doctrine_mongodb.odm.document_manager');
                $configurer->configure($entityManager->getMetadataFactory());
            }
        }
    }
}
