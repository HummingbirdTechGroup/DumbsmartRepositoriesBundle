<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\ObjectIdentifier;

class MetadataFactory
{
    const ORM_TO_ONE_BITMASK = 3;
    const ORM_TO_MANY_BITMASK = 12;
    const ODM_TO_ONE_VALUE = 'one';
    const ODM_TO_MANY_VALUE = 'many';

    /**
     * @param ClassMetadata    $doctrineMetadata
     * @param ObjectIdentifier $identifier
     *
     * @return Metadata
     */
    public function createMetadata(ClassMetadata $doctrineMetadata, ObjectIdentifier $identifier)
    {
        $metadata = new Metadata($identifier);

        foreach ($doctrineMetadata->associationMappings as $association) {
            if ($this->isToOneRelation($association)) {
                $metadata->setRelation(new OneToOneRelation($association['fieldName']));
            } elseif ($this->isToManyRelation($association)) {
                $metadata->setRelation(new OneToManyRelation($association['fieldName']));
            }
        }

        return $metadata;
    }

    /**
     * @param array $association
     *
     * @return bool
     */
    private function isToOneRelation(array $association)
    {
        if ($this->isOrmRelation($association)) {
            return ($association['type'] & self::ORM_TO_ONE_BITMASK) === $association['type'];
        } elseif ($this->isOdmRelation($association)) {
            return $association['reference'] && $association['type'] === self::ODM_TO_ONE_VALUE;
        }

        return false;
    }

    /**
     * @param array $association
     *
     * @return bool
     */
    private function isToManyRelation(array $association)
    {
        if ($this->isOrmRelation($association)) {
            return ($association['type'] & self::ORM_TO_MANY_BITMASK) === $association['type'];
        } elseif ($this->isOdmRelation($association)) {
            return $association['reference'] && $association['type'] === self::ODM_TO_MANY_VALUE;
        }

        return false;
    }

    /**
     * @param array $association
     *
     * @return bool
     */
    private function isOrmRelation(array $association)
    {
        return array_key_exists('type', $association) &&
               array_key_exists('fieldName', $association) &&
               is_int($association['type']) &&
               is_string($association['fieldName'])
        ;
    }

    /**
     * @param array $association
     *
     * @return bool
     */
    private function isOdmRelation(array $association)
    {
        return array_key_exists('type', $association) &&
               array_key_exists('reference', $association) &&
               array_key_exists('fieldName', $association) &&
               is_string($association['type']) &&
               is_bool($association['reference']) &&
               is_string($association['fieldName'])
        ;
    }
}
