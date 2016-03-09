<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Everzet\PersistedObjects\InMemoryRepository;
use Everzet\PersistedObjects\ObjectIdentifier;

class Configurer
{
    const ORM_TO_ONE_BITMASK = 3;
    const ORM_TO_MANY_BITMASK = 12;
    const ODM_TO_ONE_VALUE = 'one';
    const ODM_TO_MANY_VALUE = 'many';

    /**
     * @var MetadataManager
     */
    private $metadataManager;

    /**
     * @var RepositoryManager
     */
    private $repositoryManager;

    /**
     * @param MetadataManager      $metadataManager
     * @param RepositoryManager    $repositoryManager
     */
    public function __construct(
        MetadataManager $metadataManager,
        RepositoryManager $repositoryManager
    ) {
        $this->metadataManager = $metadataManager;
        $this->repositoryManager = $repositoryManager;
    }

    /**
     * @param ClassMetadataFactory $metadataFactory
     */
    public function configure(ClassMetadataFactory $metadataFactory)
    {
        foreach ($metadataFactory->getAllMetadata() as $doctrineMetadata) {
            $identifier = new DoctrineObjectIdentifier($doctrineMetadata);

            $this->metadataManager->addMetadata(
                $doctrineMetadata->getName(),
                $this->createMetadata($doctrineMetadata, $identifier)
            );

            $this->repositoryManager->addRepository(
                $doctrineMetadata->getName(),
                $this->createRepository($identifier)
            );
        }
    }

    /**
     * @param ClassMetadata    $doctrineMetadata
     * @param ObjectIdentifier $identifier
     *
     * @return Metadata
     */
    private function createMetadata(ClassMetadata $doctrineMetadata, ObjectIdentifier $identifier)
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
            return !$association['embedded'] && $association['type'] === self::ODM_TO_ONE_VALUE;
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
            return !$association['embedded'] && $association['type'] === self::ODM_TO_MANY_VALUE;
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
               array_key_exists('embedded', $association) &&
               array_key_exists('fieldName', $association) &&
               is_string($association['type']) &&
               is_bool($association['embedded']) &&
               is_string($association['fieldName'])
        ;
    }

    /**
     * @param ObjectIdentifier $identifier
     *
     * @return InMemoryRepository
     */
    private function createRepository(ObjectIdentifier $identifier)
    {
        return new InMemoryRepository($identifier);
    }
}
