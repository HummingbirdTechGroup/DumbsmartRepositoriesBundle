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
     * @param string $className
     * @param string $property
     *
     * @return PropertyObjectIdentifier
     */
    public function createPropertyObjectIdentifier($className, $property)
    {
        return new PropertyObjectIdentifier($className, $property);
    }
}
