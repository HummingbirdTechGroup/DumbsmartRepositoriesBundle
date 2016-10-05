<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\AliasedObjectIdentifier;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\AliasedMetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\MetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\ObjectIdentifierFactory;
use carlosV2\DumbsmartRepositoriesBundle\Configurer\RepositoryFactory;
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
        MetadataFactory $mf,
        RepositoryFactory $rf,
        AliasedMetadataFactory $amf
    ) {
        $this->beConstructedWith($mm, $rm, $oif, $mf, $rf, $amf);
    }

    function it_configures_new_entities_without_inheritance(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        MetadataFactory $mf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException');
        $rm->getRepositoryForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $oif->createObjectIdentifier($classMetadata)->willReturn($identifier);
        $mf->createMetadata($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\AliasedObjectIdentifier'))->willReturn($metadata);
        $rf->createRepository($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\AliasedObjectIdentifier'))->willReturn($repository);

        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->parentClasses = [];

        $mm->addMetadata('my_class', $metadata)->shouldBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configureEntities($factory);
    }

    function it_reuses_configuration_for_already_configured_entities_without_inheritance(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        MetadataFactory $mf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willReturn($metadata);
        $rm->getRepositoryForClassName('my_class')->willReturn($repository);
        $oif->createObjectIdentifier($classMetadata)->willReturn($identifier);
        $mf->createMetadata($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\AliasedObjectIdentifier'))->shouldNotBeCalled();
        $rf->createRepository($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\AliasedObjectIdentifier'))->shouldNotBeCalled();

        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->parentClasses = [];

        $mm->addMetadata('my_class', $metadata)->shouldNotBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configureEntities($factory);
    }

    function it_configures_the_entities_with_inheritance(
        ClassMetadataFactory $factory,
        ClassMetadata $classMetadata,
        ClassMetadata $parentClassMetadata,
        MetadataManager $mm,
        RepositoryManager $rm,
        ObjectIdentifierFactory $oif,
        ObjectIdentifier $identifier,
        MetadataFactory $mf,
        Metadata $metadata,
        RepositoryFactory $rf,
        Repository $repository
    ) {
        $mm->getMetadataForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\MetadataNotFoundException');
        $rm->getRepositoryForClassName('my_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $rm->getRepositoryForClassName('my_parent_class')->willThrow('carlosV2\DumbsmartRepositories\Exception\RepositoryNotFoundException');
        $oif->createObjectIdentifier($classMetadata)->willReturn($identifier);
        $mf->createMetadata($classMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\AliasedObjectIdentifier'))->willReturn($metadata);
        $rf->createRepository($parentClassMetadata, Argument::type('carlosV2\DumbsmartRepositoriesBundle\AliasedObjectIdentifier'))->willReturn($repository);

        $factory->getAllMetadata()->willReturn([$classMetadata]);
        $factory->getMetadataFor('my_parent_class')->willReturn($parentClassMetadata);
        $classMetadata->getName()->willReturn('my_class');
        $classMetadata->parentClasses = ['my_parent_class'];

        $mm->addMetadata('my_parent_class', $metadata)->shouldNotBeCalled();
        $mm->addMetadata('my_class', $metadata)->shouldBeCalled();
        $rm->addRepository('my_parent_class', $repository)->shouldBeCalled();
        $rm->addRepository('my_class', $repository)->shouldBeCalled();

        $this->configureEntities($factory);
    }

    function it_reuses_configuration_for_the_aliases(
        MetadataManager $mm,
        Metadata $metadata,
        Metadata $aliasedMetadata,
        RepositoryManager $rm,
        Repository $repository,
        AliasedObjectIdentifier $identifier,
        AliasedMetadataFactory $amf
    ) {
        $amf->createAliasedMetadata(['class' => 'my_class', 'fields' => []])->willReturn($aliasedMetadata);

        $metadata->getObjectIdentifier()->willReturn($identifier);
        $identifier->setAlias('my_alias')->shouldBeCalled();

        $mm->getMetadataForClassName('my_class')->willReturn($metadata);
        $mm->addMetadata('my_alias', $aliasedMetadata)->shouldBeCalled();

        $rm->getRepositoryForClassName('my_class')->willReturn($repository);
        $rm->addRepository('my_alias', $repository)->shouldBeCalled();

        $this->configureAliases(['my_alias' => ['class' => 'my_class', 'fields' => []]]);
    }
}
