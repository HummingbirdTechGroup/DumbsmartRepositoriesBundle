<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\Metadata\EntityMetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\ObjectIdentifierFactory;
use carlosV2\DumbsmartRepositoriesBundle\Repository\RepositoryFactory;
use Everzet\PersistedObjects\ObjectIdentifier;
use Everzet\PersistedObjects\Repository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class EntitiesConfigurerSpec extends ObjectBehavior
{
    function let(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        EntityMetadataFactory $emf,
        RepositoryFactory $rf
    ) {
        $this->beConstructedWith($mm, $rm, $oif, $emf, $rf);
    }

    function it_configures_new_entities_without_inheritance(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        EntityMetadataFactory $emf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException');
        $rm->getRepositoryForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $oif->createPropertyObjectIdentifier('my_class', 'my_id_field')->willReturn($identifier);
        $emf->createMetadata(['relations'], Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($metadata);
        $rf->createRepository('my_class', Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($repository);

        $mm->addMetadata('my_class', $metadata)->shouldBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configure([
            'my_class' => [
                'id' => 'my_id_field',
                'relations' => ['relations']
            ]
        ]);
    }

    function it_reuses_configuration_for_already_configured_entities_without_inheritance(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        EntityMetadataFactory $emf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willReturn($metadata);
        $rm->getRepositoryForClassName('my_class')->willReturn($repository);
        $oif->createPropertyObjectIdentifier('my_class', 'my_id_field')->willReturn($identifier);
        $emf->createMetadata(['relations'], Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->shouldNotBeCalled();
        $rf->createRepository('my_class', Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->shouldNotBeCalled();

        $mm->addMetadata('my_class', $metadata)->shouldNotBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configure([
            'my_class' => [
                'id' => 'my_id_field',
                'relations' => ['relations']
            ]
        ]);
    }

    function it_configures_the_entities_with_inheritance(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        EntityMetadataFactory $emf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_child_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException');
        $mm->getMetadataForClassName('my_parent_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException');
        $rm->getRepositoryForClassName('my_child_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $rm->getRepositoryForClassName('my_parent_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $oif->createPropertyObjectIdentifier('my_parent_class', 'my_id_field')->willReturn($identifier);
        $emf->createMetadata(['parent_relations', 'child_relations'], Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($metadata);
        $emf->createMetadata(['parent_relations'], Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($metadata);
        $rf->createRepository('my_parent_class', Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($repository);

        $mm->addMetadata('my_parent_class', $metadata)->shouldBeCalled();
        $mm->addMetadata('my_child_class', $metadata)->shouldBeCalled();
        $rm->addRepository('my_parent_class', $repository)->shouldBeCalled();
        $rm->addRepository('my_child_class', $repository)->shouldBeCalled();

        $this->configure([
            'my_parent_class' => [
                'id' => 'my_id_field',
                'relations' => ['parent_relations']
            ],
            'my_child_class' => [
                'extends' => 'my_parent_class',
                'relations' => ['child_relations']
            ]
        ]);
    }
}
