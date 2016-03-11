<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle;

use carlosV2\DumbsmartRepositories\RepositoryManager;
use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Everzet\PersistedObjects\FileRepository;
use Everzet\PersistedObjects\InMemoryRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RepositoryConfigurerSpec extends ObjectBehavior
{
    function let(ClassMetadata $metadata)
    {
        $metadata->getName()->willReturn('my_class');
    }

    function it_configures_an_InMemoryRepository(
        ClassMetadataFactory $factory,
        RepositoryManager $manager,
        ClassMetadata $metadata
    ) {
        $factory->getAllMetadata()->willReturn([$metadata]);
        $manager->addRepository('my_class', Argument::allOf(
            Argument::type(InMemoryRepository::class),
            Argument::that(function (InMemoryRepository $repository) use ($metadata) {
                return $this->getProperty($repository, 'identifier') == new DoctrineObjectIdentifier($metadata->getWrappedObject());
            })
        ))->shouldBeCalled();

        $this->beConstructedWith($manager, 'in_memory');
        $this->configureRepositories($factory);
    }

    function it_configures_a_FileRepository(
        ClassMetadataFactory $factory,
        RepositoryManager $manager,
        ClassMetadata $metadata
    ) {
        $factory->getAllMetadata()->willReturn([$metadata]);
        $manager->addRepository('my_class', Argument::allOf(
            Argument::type(FileRepository::class),
            Argument::that(function (FileRepository $repository) use ($metadata) {
                if ($this->getProperty($repository, 'filename') !== 'my/path/' . md5('my_class')) {
                    return false;
                }

                return $this->getProperty($repository, 'identifier') == new DoctrineObjectIdentifier($metadata->getWrappedObject());
            })
        ))->shouldBeCalled();

        $this->beConstructedWith($manager, 'file', 'my/path');
        $this->configureRepositories($factory);
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

    public function getMatchers()
    {
        return [
            'returnFileRepository' => function (FileRepository $repository, $expectedFileName) {
                $property = new \ReflectionProperty($repository, 'filename');
                $property->setAccessible(true);

                return $property->getValue($repository) === $expectedFileName;
            }
        ];
    }
}
