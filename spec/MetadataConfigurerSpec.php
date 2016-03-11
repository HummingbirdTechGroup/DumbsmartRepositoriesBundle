<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\Relation\OneToManyRelation;
use carlosV2\DumbsmartRepositories\Relation\OneToOneRelation;
use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MetadataConfigurerSpec extends ObjectBehavior
{
    function let(MetadataManager $manager)
    {
        $this->beConstructedWith($manager);
    }

    function it_configures_the_entities_accordingly_from_an_ORM_schema(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $manager
    ) {
        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->associationMappings = [
            'toOneField' => ['type' => 1, 'fieldName' => 'toOneField'],
            'toManyField' => ['type' => 4, 'fieldName' => 'toManyField']
        ];

        $manager->addMetadata('my_class', Argument::allOf(
            Argument::type(Metadata::class),
            Argument::that(function (Metadata $metadata) use ($classMetadata) {
                if ($this->getProperty($metadata, 'identifier') != new DoctrineObjectIdentifier($classMetadata->getWrappedObject())) {
                    return false;
                }

                return $this->getProperty($metadata, 'relations') == [
                    new OneToOneRelation('toOneField'),
                    new OneToManyRelation('toManyField')
                ];
            })
        ))->shouldBeCalled();

        $this->configureMetadata($factory);
    }

    function it_configures_the_entities_accordingly_from_an_ODM_schema(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $manager
    ) {
        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->associationMappings = [
            'toOneField' => ['related' => true, 'type' => 'one', 'fieldName' => 'toOneField'],
            'toManyField' => ['related' => true, 'type' => 'many', 'fieldName' => 'toManyField']
        ];

        $manager->addMetadata('my_class', Argument::allOf(
            Argument::type(Metadata::class),
            Argument::that(function (Metadata $metadata) use ($classMetadata) {
                if ($this->getProperty($metadata, 'identifier') != new DoctrineObjectIdentifier($classMetadata->getWrappedObject())) {
                    return false;
                }

                return $this->getProperty($metadata, 'relations') == [
                    new OneToOneRelation('toOneField'),
                    new OneToManyRelation('toManyField')
                ];
            })
        ))->shouldBeCalled();

        $this->configureMetadata($factory);
    }

    function it_does_not_use_an_embedded_relations_on_ODM_schemas(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $manager
    ) {
        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->associationMappings = [
            'toOneField' => ['related' => false, 'type' => 'one', 'fieldName' => 'toOneField'],
            'toManyField' => ['related' => true, 'type' => 'many', 'fieldName' => 'toManyField']
        ];

        $manager->addMetadata('my_class', Argument::allOf(
            Argument::type(Metadata::class),
            Argument::that(function (Metadata $metadata) use ($classMetadata) {
                if ($this->getProperty($metadata, 'identifier') != new DoctrineObjectIdentifier($classMetadata->getWrappedObject())) {
                    return false;
                }

                return $this->getProperty($metadata, 'relations') == [
                    new OneToManyRelation('toManyField')
                ];
            })
        ))->shouldBeCalled();

        $this->configureMetadata($factory);
    }

    function it_sets_the_entity_metadata_even_without_relations(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $manager
    ) {
        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->associationMappings = [];

        $manager->addMetadata('my_class', Argument::allOf(
            Argument::type(Metadata::class),
            Argument::that(function (Metadata $metadata) use ($classMetadata) {
                if ($this->getProperty($metadata, 'identifier') != new DoctrineObjectIdentifier($classMetadata->getWrappedObject())) {
                    return false;
                }

                return $this->getProperty($metadata, 'relations') == [];
            })
        ))->shouldBeCalled();

        $this->configureMetadata($factory);
    }

    function it_ignores_invalid_relations(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $manager
    ) {
        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->associationMappings = [
            'ormFieldWithoutType' => ['fieldName' => 'toOneField'],
            'ormFieldWithoutFieldName' => ['type' => 1],
            'ormFieldWithWrongType' => ['type' => '4', 'fieldName' => 'toOneField'],
            'ormFieldWithWrongFieldName' => ['type' => 4, 'fieldName' => 1],

            'odmFieldWithoutType' => ['related' => false, 'fieldName' => 'toOneField'],
            'odmFieldWithoutRelated' => ['type' => 'one', 'fieldName' => 'toOneField'],
            'odmFieldWithoutFieldName' => ['related' => false, 'type' => 'one'],
            'odmFieldWithWrongType' => ['related' => false, 'type' => '1', 'fieldName' => 'toOneField'],
            'odmFieldWithWrongRelated' => ['related' => 'false', 'type' => 'one', 'fieldName' => 'toOneField'],
            'odmFieldWitWrongFieldName' => ['related' => false, 'type' => 'one', 'fieldName' => 1]
        ];

        $manager->addMetadata('my_class', Argument::allOf(
            Argument::type(Metadata::class),
            Argument::that(function (Metadata $metadata) use ($classMetadata) {
                if ($this->getProperty($metadata, 'identifier') != new DoctrineObjectIdentifier($classMetadata->getWrappedObject())) {
                    return false;
                }

                return $this->getProperty($metadata, 'relations') == [];
            })
        ))->shouldBeCalled();

        $this->configureMetadata($factory);
    }

    /**
     * @param object $object
     * @param string $property
     *
     * @return mixed
     */
    private function getProperty($object, $property)
    {
        $property = new \ReflectionProperty($object, $property);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}
