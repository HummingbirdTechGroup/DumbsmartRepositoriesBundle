<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class ObjectIdentifierFactory
{
    /**
     * @param ClassMetadata $metadata
     *
     * @return DoctrineObjectIdentifier
     */
    public function createObjectIdentifier(ClassMetadata $metadata)
    {
        return new DoctrineObjectIdentifier($metadata);
    }
}
