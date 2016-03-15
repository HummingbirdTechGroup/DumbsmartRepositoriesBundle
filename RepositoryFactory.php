<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\Repository;

interface RepositoryFactory
{
    /**
     * @param ClassMetadata $metadata
     *
     * @return Repository
     */
    public function createRepository(ClassMetadata $metadata);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports($type);
}
