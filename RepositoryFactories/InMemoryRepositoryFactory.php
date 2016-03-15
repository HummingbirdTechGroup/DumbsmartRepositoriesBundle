<?php

namespace carlosV2\DumbsmartRepositoriesBundle\RepositoryFactories;

use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use carlosV2\DumbsmartRepositoriesBundle\RepositoryFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\InMemoryRepository;

class InMemoryRepositoryFactory implements RepositoryFactory
{
    const TYPE = 'in_memory';

    /**
     * {@inheritdoc}
     */
    public function createRepository(ClassMetadata $metadata)
    {
        return new InMemoryRepository(new DoctrineObjectIdentifier($metadata));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        return $type === self::TYPE;
    }
}
