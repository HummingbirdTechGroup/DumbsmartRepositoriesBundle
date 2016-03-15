<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\RepositoryFactories;

use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use carlosV2\DumbsmartRepositoriesBundle\RepositoryFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\FileRepository;
use PhpSpec\ObjectBehavior;

class FileRepositoryFactorySpec extends ObjectBehavior
{
    function let(ClassMetadata $metadata)
    {
        $metadata->getName()->willReturn('my\class\namespace');
        $this->beConstructedWith('my/directory');
    }

    function it_is_a_RepositoryFactory()
    {
        $this->shouldHaveType(RepositoryFactory::class);
    }

    function it_creates_new_FileRepository(ClassMetadata $metadata)
    {
        $this->createRepository($metadata)->shouldBeAnInstanceOf(FileRepository::class);
        $this->createRepository($metadata)->shouldHaveDoctrineObjectIdentifierFor($metadata);
        $this->createRepository($metadata)->shouldHaveFileName('my/directory' . DIRECTORY_SEPARATOR . 'my_class_namespace.repository');
    }

    function it_supports_the_file_type()
    {
        $this->beConstructedWith([]);
        $this->supports('file')->shouldReturn(true);
        $this->supports('other')->shouldReturn(false);
    }

    public function getMatchers()
    {
        return [
            'haveDoctrineObjectIdentifierFor' => function ($repository, $metadata) {
                $property = new \ReflectionProperty($repository, 'identifier');
                $property->setAccessible(true);
                return $property->getValue($repository) == new DoctrineObjectIdentifier($metadata);
            },

            'haveFileName' => function ($repository, $fileName) {
                $property = new \ReflectionProperty($repository, 'filename');
                $property->setAccessible(true);
                return $property->getValue($repository) === $fileName;
            }
        ];
    }
}
