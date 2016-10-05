<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\Metadata\DoctrineMetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\ObjectIdentifierFactory;
use carlosV2\DumbsmartRepositoriesBundle\Repository\RepositoryFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Everzet\PersistedObjects\ObjectIdentifier;
use Everzet\PersistedObjects\Repository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DoctrineConfigurerSpec extends ObjectBehavior
{
    function let(
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        DoctrineMetadataFactory $dmf,
        RepositoryFactory $rf
    ) {
        $this->beConstructedWith($mm, $rm, $oif, $dmf, $rf);
    }

    function it_configures_new_entities_without_inheritance(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        DoctrineMetadataFactory $dmf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException');
        $rm->getRepositoryForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $oif->createDoctrineObjectIdentifier($classMetadata)->willReturn($identifier);
        $dmf->createMetadata($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($metadata);
        $rf->createRepository('my_class', Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($repository);

        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->parentClasses = [];

        $mm->addMetadata('my_class', $metadata)->shouldBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configure($factory);
    }

    function it_reuses_configuration_for_already_configured_entities_without_inheritance(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        DoctrineMetadataFactory $dmf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willReturn($metadata);
        $rm->getRepositoryForClassName('my_class')->willReturn($repository);
        $oif->createDoctrineObjectIdentifier($classMetadata)->willReturn($identifier);
        $dmf->createMetadata($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->shouldNotBeCalled();
        $rf->createRepository('my_class', Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->shouldNotBeCalled();

        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->parentClasses = [];

        $mm->addMetadata('my_class', $metadata)->shouldNotBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configure($factory);
    }

    function it_configures_the_entities_with_inheritance(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        ClassMetadata $parentClassMetadata,
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        DoctrineMetadataFactory $dmf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException');
        $rm->getRepositoryForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $rm->getRepositoryForClassName('my_parent_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $oif->createDoctrineObjectIdentifier($classMetadata)->willReturn($identifier);
        $dmf->createMetadata($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($metadata);
        $rf->createRepository('my_parent_class', Argument::type('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier'))->willReturn($repository);

        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $factory->getMetadataFor('my_parent_class')->willReturn($parentClassMetadata);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->parentClasses = ['my_parent_class'];
        $parentClassMetadata->getName()->willReturn('my_parent_class');

        $mm->addMetadata('my_parent_class', $metadata)->shouldNotBeCalled();
        $mm->addMetadata('my_class', $metadata)->shouldBeCalled();
        $rm->addRepository('my_parent_class', $repository)->shouldBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configure($factory);
    }
}
