<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle;

use Everzet\PersistedObjects\ObjectIdentifier;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AliasedObjectIdentifierSpec extends ObjectBehavior
{
    function let(ObjectIdentifier $identifier)
    {
        $this->beConstructedWith(\stdClass::class, $identifier);
    }

    function it_is_an_ObjectIdentifier()
    {
        $this->shouldHaveType(ObjectIdentifier::class);
    }

    function it_returns_the_identifier_of_the_object(ObjectIdentifier $identifier)
    {
        $object = new \stdClass();

        $identifier->getIdentity($object)->willReturn('123');

        $this->getIdentity($object)->shouldReturn('123');
    }

    function it_returns_the_identifier_of_an_aliased_object(ObjectIdentifier $identifier)
    {
        $object = new \DateTime();

        $identifier->getIdentity(Argument::type(\stdClass::class))->willReturn('123');

        $this->setAlias(\DateTime::class);
        $this->getIdentity($object)->shouldReturn('123');
    }
}
