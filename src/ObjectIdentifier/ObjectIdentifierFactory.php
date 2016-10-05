<?php

namespace carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;

class ObjectIdentifierFactory
{
    /**
     * @param ClassMetadata $metadata
     *
     * @return DoctrineObjectIdentifier
     */
    public function createDoctrineObjectIdentifier(ClassMetadata $metadata)
    {
        return new DoctrineObjectIdentifier($metadata);
    }

    /**
     * @param string $property
     *
     * @return PropertyObjectIdentifier
     */
    public function createPropertyObjectIdentifier($property)
    {
        return new PropertyObjectIdentifier($property);
    }
}
