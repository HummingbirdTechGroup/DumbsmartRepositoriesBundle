<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

class ObjectIdentifierFactorySpec extends ObjectBehavior
{
    function it_creates_a_DoctrineObjectIdentifier(ClassMetadata $metadata)
    {
        $this->createDoctrineObjectIdentifier($metadata)->shouldBeAnInstanceOf('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\DoctrineObjectIdentifier');
    }

    function it_creates_a_PropertyObjectIdentifier()
    {
        $this->createPropertyObjectIdentifier('m_class', 'property')->shouldBeAnInstanceOf('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\PropertyObjectIdentifier');
    }
}
