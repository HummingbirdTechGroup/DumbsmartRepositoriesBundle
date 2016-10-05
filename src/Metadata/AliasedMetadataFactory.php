<?php

namespace carlosV2\DumbsmartRepositoriesBundle\Metadata;

use carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException;
use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use carlosV2\DumbsmartRepositories\Relation\Relation;

class AliasedMetadataFactory
{
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
     * @param array $alias
     *
     * @return Metadata
     *
     * @throws MetadataNotFoundException
     */
    public function createMetadata(array $alias)
    {
        $metadata = $this->manager->getMetadataForClassName($alias['class']);
        $aliasedMetadata = new Metadata($metadata->getObjectIdentifier());

        foreach ($metadata->getRelations() as $relation) {
            if (array_key_exists($relation->getField(), $alias['mapping'])) {
                $aliasedMetadata->setRelation(
                    $this->createAliasedRelation(
                        $alias['mapping'][$relation->getField()],
                        $relation
                    )
                );
            } else {
                $aliasedMetadata->setRelation($relation);
            }
        }

        return $aliasedMetadata;
    }

    /**
     * @param string   $field
     * @param Relation $relation
     *
     * @return Relation
     */
    private function createAliasedRelation($field, Relation $relation)
    {
        if ($relation instanceof OneToOneRelation) {
            return new OneToOneRelation($field);
        } elseif ($relation instanceof OneToManyRelation) {
            return new OneToManyRelation($field);
        }
    }
}
