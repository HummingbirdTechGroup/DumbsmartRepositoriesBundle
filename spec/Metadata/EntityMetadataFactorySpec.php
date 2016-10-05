<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Metadata;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use carlosV2\DumbsmartRepositoriesBundle\Metadata\EntityMetadataFactory;
use Everzet\PersistedObjects\ObjectIdentifier;
use PhpSpec\ObjectBehavior;

class EntityMetadataFactorySpec extends ObjectBehavior
{
    function it_creates_metadata_without_relations(ObjectIdentifier $identifier)
    {
        $this->createMetadata([], $identifier)->shouldReturnMetadataWith($identifier, []);
    }

    function it_creates_metadata_with_relations(ObjectIdentifier $identifier)
    {
        $this->createMetadata([
            'my_field_1' => EntityMetadataFactory::ONE_TO_ONE_RELATION,
            'my_field_2' => EntityMetadataFactory::ONE_TO_MANY_RELATION
        ], $identifier)->shouldReturnMetadataWith($identifier, [
            new OneToOneRelation('my_field_1'),
            new OneToManyRelation('my_field_2')
        ]);
    }

    public function getMatchers()
    {
        return [
            'returnMetadataWith' => function ($metadata, $identifier, $relations) {
                if (!$metadata instanceof Metadata) {
                    return false;
                }

                if ($metadata->getObjectIdentifier() !== $identifier) {
                    return false;
                }

                return $metadata->getRelations() == $relations;
            }
        ];
    }
}
