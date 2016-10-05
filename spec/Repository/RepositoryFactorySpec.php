<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Repository;

use Everzet\PersistedObjects\ObjectIdentifier;
use PhpSpec\ObjectBehavior;

class RepositoryFactorySpec extends ObjectBehavior
{
    function it_creates_an_InMemoryRepository(ObjectIdentifier $identifier)
    {
        $this->beConstructedWith('in_memory', '');
        $this->createRepository('My\Class\Namespace', $identifier)->shouldBeAnInstanceOf('Everzet\PersistedObjects\InMemoryRepository');
        $this->createRepository('My\Class\Namespace', $identifier)->shouldHaveObjectIdentifier($identifier);
    }

    function it_creates_an_FileRepository(ObjectIdentifier $identifier)
    {
        $this->beConstructedWith('file', 'my/directory');
        $this->createRepository('My\Class\Namespace', $identifier)->shouldBeAnInstanceOf('Everzet\PersistedObjects\FileRepository');
        $this->createRepository('My\Class\Namespace', $identifier)->shouldHaveObjectIdentifier($identifier);
        $this->createRepository('My\Class\Namespace', $identifier)->shouldHaveFileName('my/directory' . DIRECTORY_SEPARATOR . 'My_Class_Namespace.repository');
    }

    function it_defaults_to_InMemoryRepository_if_unknown_type_is_provided(ObjectIdentifier $identifier)
    {
        $this->beConstructedWith('unknown', '');
        $this->createRepository('My\Class\Namespace', $identifier)->shouldBeAnInstanceOf('Everzet\PersistedObjects\InMemoryRepository');
        $this->createRepository('My\Class\Namespace', $identifier)->shouldHaveObjectIdentifier($identifier);
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
