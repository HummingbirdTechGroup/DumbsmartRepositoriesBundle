<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\RepositoryFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Everzet\PersistedObjects\Repository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RepositoryConfigurerSpec extends ObjectBehavior
{
    function let(RepositoryManager $manager, RepositoryFactory $factory1, RepositoryFactory $factory2)
    {
        $this->beConstructedWith($manager, 'type');
        $this->addRepositoryFactory($factory1);
        $this->addRepositoryFactory($factory2);
    }

    function it_assigns_a_repository_for_each_entity_found(
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $metadata1,
        ClassMetadata $metadata2,
        RepositoryManager $manager,
        RepositoryFactory $factory1,
        RepositoryFactory $factory2,
        Repository $repository1,
        Repository $repository2
    ) {
        $factory1->supports('type')->willReturn(false);
        $factory2->supports('type')->willReturn(true);
        $factory2->createRepository($metadata1)->willReturn($repository1);
        $factory2->createRepository($metadata2)->willReturn($repository2);

        $metadataFactory->getAllMetadata()->willReturn([$metadata1, $metadata2]);
        $metadata1->getName()->willReturn('my_first_class');
        $metadata2->getName()->willReturn('my_second_class');

        $manager->addRepository('my_first_class', $repository1)->shouldBeCalled();
        $manager->addRepository('my_second_class', $repository2)->shouldBeCalled();

        $this->configureRepositories($metadataFactory);
    }

    function it_throws_an_exception_if_there_is_not_a_RepositoryFactory_that_supports_the_type(
        ClassMetadataFactory $metadataFactory,
        ClassMetadata $metadata1,
        ClassMetadata $metadata2,
        RepositoryFactory $factory1,
        RepositoryFactory $factory2
    ) {
        $factory1->supports('type')->willReturn(false);
        $factory2->supports('type')->willReturn(false);

        $metadataFactory->getAllMetadata()->willReturn([$metadata1, $metadata2]);
        $metadata1->getName()->willReturn('my_first_class');
        $metadata2->getName()->willReturn('my_second_class');

        $this->shouldThrow(\InvalidArgumentException::class)->duringConfigureRepositories($metadataFactory);
    }
}
