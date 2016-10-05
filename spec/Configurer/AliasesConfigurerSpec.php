<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositories\Metadata;
use carlosV2\DumbsmartRepositories\MetadataManager;
use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\Metadata\AliasedMetadataFactory;
use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier;
use Everzet\PersistedObjects\Repository;
use PhpSpec\ObjectBehavior;

class AliasesConfigurerSpec extends ObjectBehavior
{
    function let(
        MetadataManager $mm,
        RepositoryManager $rm,
        AliasedMetadataFactory $amf
    ) {
        $this->beConstructedWith($mm, $rm, $amf);
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
        $amf->createMetadata(['class' => 'my_class', 'fields' => []])->willReturn($aliasedMetadata);

        $metadata->getObjectIdentifier()->willReturn($identifier);
        $identifier->setAlias('my_alias')->shouldBeCalled();

        $mm->getMetadataForClassName('my_class')->willReturn($metadata);
        $mm->addMetadata('my_alias', $aliasedMetadata)->shouldBeCalled();

        $rm->getRepositoryForClassName('my_class')->willReturn($repository);
        $rm->addRepository('my_alias', $repository)->shouldBeCalled();

        $this->configure(['my_alias' => ['class' => 'my_class', 'fields' => []]]);
    }
}
