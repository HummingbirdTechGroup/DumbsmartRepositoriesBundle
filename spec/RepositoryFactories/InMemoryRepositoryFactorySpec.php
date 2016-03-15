<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\RepositoryFactories;

use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use carlosV2\DumbsmartRepositoriesBundle\RepositoryFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\InMemoryRepository;
use PhpSpec\ObjectBehavior;

class InMemoryRepositoryFactorySpec extends ObjectBehavior
{
    function it_is_a_RepositoryFactory()
    {
        $this->shouldHaveType(RepositoryFactory::class);
    }

    function it_creates_new_InMemoryRepository(ClassMetadata $metadata)
    {
        $this->createRepository($metadata)->shouldBeAnInstanceOf(InMemoryRepository::class);
        $this->createRepository($metadata)->shouldHaveDoctrineObjectIdentifierFor($metadata);
    }

    function it_supports_the_in_memory_type()
    {
        $this->supports('in_memory')->shouldReturn(true);
        $this->supports('other')->shouldReturn(false);
    }

    public function getMatchers()
    {
        return [
            'haveDoctrineObjectIdentifierFor' => function ($repository, $metadata) {
                $property = new \ReflectionProperty($repository, 'identifier');
                $property->setAccessible(true);
                return $property->getValue($repository) == new DoctrineObjectIdentifier($metadata);
            }
        ];
    }
}
