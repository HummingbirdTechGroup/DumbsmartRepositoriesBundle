<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Configurer;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\FileRepository;
use Everzet\PersistedObjects\InMemoryRepository;
use Everzet\PersistedObjects\ObjectIdentifier;
use PhpSpec\ObjectBehavior;

class RepositoryFactorySpec extends ObjectBehavior
{
    function let(ClassMetadata $metadata)
    {
        $metadata->getName()->willReturn('My\Class\Namespace');
    }

    function it_creates_an_InMemoryRepository(
        ClassMetadata $metadata,
        ObjectIdentifier $identifier
    ) {
        $this->beConstructedWith('in_memory', '');
        $this->createRepository($metadata, $identifier)->shouldBeAnInstanceOf(InMemoryRepository::class);
        $this->createRepository($metadata, $identifier)->shouldHaveObjectIdentifier($identifier);
    }

    function it_creates_an_FileRepository(
        ClassMetadata $metadata,
        ObjectIdentifier $identifier
    ) {
        $this->beConstructedWith('file', 'my/directory');
        $this->createRepository($metadata, $identifier)->shouldBeAnInstanceOf(FileRepository::class);
        $this->createRepository($metadata, $identifier)->shouldHaveObjectIdentifier($identifier);
        $this->createRepository($metadata, $identifier)->shouldHaveFileName('my/directory' . DIRECTORY_SEPARATOR . 'My_Class_Namespace.repository');
    }

    function it_defaults_to_InMemoryRepository_if_unknown_type_is_provided(
        ClassMetadata $metadata,
        ObjectIdentifier $identifier
    ) {
        $this->beConstructedWith('unknown', '');
        $this->createRepository($metadata, $identifier)->shouldBeAnInstanceOf(InMemoryRepository::class);
        $this->createRepository($metadata, $identifier)->shouldHaveObjectIdentifier($identifier);
    }

    public function getMatchers()
    {
        return [
            'haveObjectIdentifier' => function ($repository, $identifier) {
                $property = new \ReflectionProperty($repository, 'identifier');
                $property->setAccessible(true);
                return $property->getValue($repository) === $identifier;
            },

            'haveFileName' => function ($repository, $fileName) {
                $property = new \ReflectionProperty($repository, 'filename');
                $property->setAccessible(true);
                return $property->getValue($repository) === $fileName;
            }
        ];
    }
}
