<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use Everzet\PersistedObjects\ObjectIdentifier;
use PhpSpec\ObjectBehavior;

class AliasedMetadataFactorySpec extends ObjectBehavior
{
    function let(MetadataManager $manager, Metadata $metadata, ObjectIdentifier $identifier)
    {
        $manager->getMetadataForClassName('my_class')->willReturn($metadata);
        $metadata->getObjectIdentifier()->willReturn($identifier);
        $metadata->getRelations()->willReturn([
            new OneToOneRelation('my_field_1'),
            new OneToManyRelation('my_field_2')
        ]);

        $this->beConstructedWith($manager);
    }

    function it_creates_a_replica_of_the_original_metadata(ObjectIdentifier $identifier)
    {
        $this->createAliasedMetadata(['class' => 'my_class', 'mapping' => []])->shouldReturnMetadataWith(
            $identifier,
            [new OneToOneRelation('my_field_1'), new OneToManyRelation('my_field_2')]
        );
    }

    function it_replaces_the_specified_fields_from_the_original_metadata(ObjectIdentifier $identifier)
    {
        $this->createAliasedMetadata([
            'class' => 'my_class',
            'mapping' => ['my_field_1' => 'my_aliases_field_1']
        ])->shouldReturnMetadataWith(
            $identifier,
            [new OneToOneRelation('my_aliases_field_1'), new OneToManyRelation('my_field_2')]
        );
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
