<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;

class MetadataConfigurer
{
    const ORM_TO_ONE_BITMASK = 3;
    const ORM_TO_MANY_BITMASK = 12;
    const ODM_TO_ONE_VALUE = 'one';
    const ODM_TO_MANY_VALUE = 'many';

    /**
     * @var MetadataManager
     */
    private $manager;

    /**
     * @param MetadataManager $manager
     */
    public function __construct(MetadataManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ClassMetadataFactory $factory
     */
    public function configureMetadata(ClassMetadataFactory $factory)
    {
        foreach ($factory->getAllMetadata() as $metadata) {
            $this->manager->addMetadata($metadata->getName(), $this->createMetadata($metadata));
        }
    }

    /**
     * @param ClassMetadata $doctrineMetadata
     *
     * @return Metadata
     */
    private function createMetadata(ClassMetadata $doctrineMetadata)
    {
        $metadata = new Metadata($this->createObjectIdentifier($doctrineMetadata));

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
            return $association['related'] && $association['type'] === self::ODM_TO_ONE_VALUE;
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
            return $association['related'] && $association['type'] === self::ODM_TO_MANY_VALUE;
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
               array_key_exists('related', $association) &&
               array_key_exists('fieldName', $association) &&
               is_string($association['type']) &&
               is_bool($association['related']) &&
               is_string($association['fieldName'])
        ;
    }

    /**
     * @param ClassMetadata $metadata
     *
     * @return DoctrineObjectIdentifier
     */
    private function createObjectIdentifier(ClassMetadata $metadata)
    {
        return new DoctrineObjectIdentifier($metadata);
    }
}
