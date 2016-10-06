<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier;
use Everzet\PersistedObjects\ObjectIdentifier;
use PhpSpec\ObjectBehavior;

class PropertyObjectIdentifierSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier\AliasedObjectIdentifier', 'identifier');
    }

    function it_is_an_ObjectIdentifier()
    {
        $this->shouldHaveType('Everzet\PersistedObjects\ObjectIdentifier');
    }

    function it_returns_the_identifier_of_the_object(ObjectIdentifier $identifier)
    {
        $object = new AliasedObjectIdentifier('class_name', $identifier->getWrappedObject());

        $this->getIdentity($object)->shouldReturn($identifier);
    }
}
