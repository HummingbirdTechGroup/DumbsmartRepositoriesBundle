<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Metadata;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use carlosV2\DumbsmartRepositories\Relation\Relation;
use Everzet\PersistedObjects\ObjectIdentifier;

class EntityMetadataFactory
{
    const ONE_TO_ONE_RELATION = 'one';
    const ONE_TO_MANY_RELATION = 'many';

    /**
     * @param array            $relations
     * @param ObjectIdentifier $identifier
     *
     * @return Metadata
     */
    public function createMetadata(array $relations, ObjectIdentifier $identifier)
    {
        $metadata = new Metadata($identifier);

        foreach ($relations as $field => $relation) {
            $metadata->setRelation($this->createRelation($field, $relation));
        }

        return $metadata;
    }

    /**
     * @param string $field
     * @param string $relation
     *
     * @return Relation
     */
    private function createRelation($field, $relation)
    {
        switch ($relation) {
            case self::ONE_TO_ONE_RELATION:
                return new OneToOneRelation($field);
                break;

            case self::ONE_TO_MANY_RELATION:
                return new OneToManyRelation($field);
        }
    }
}
