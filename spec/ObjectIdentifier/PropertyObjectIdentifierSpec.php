<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use PhpSpec\ObjectBehavior;

class PropertyObjectIdentifierSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('my_field');
    }

    function it_is_an_ObjectIdentifier()
    {
        $this->shouldHaveType('Everzet\PersistedObjects\ObjectIdentifier');
    }

    function it_returns_the_identifier_of_the_object()
    {
        $object = new \stdClass();
        $object->my_field = '123';

        $this->getIdentity($object)->shouldReturn('123');
    }
}
