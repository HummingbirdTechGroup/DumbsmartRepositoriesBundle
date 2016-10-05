<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

class DoctrineObjectIdentifierSpec extends ObjectBehavior
{
    function let(ClassMetadata $metadata)
    {
        $this->beConstructedWith($metadata);
    }

    function it_is_an_ObjectIdentifier()
    {
        $this->shouldHaveType('Everzet\PersistedObjects\ObjectIdentifier');
    }

    function it_computes_the_single_id_of_an_object(ClassMetadata $metadata)
    {
        $object = new \stdClass();

        $metadata->getIdentifier()->willReturn(['field']);
        $metadata->getIdentifierValues($object)->willReturn(['field1' => 'value']);

        $this->getIdentity($object)->shouldReturn('value');
    }

    function it_computes_the_composed_id_of_an_object(ClassMetadata $metadata)
    {
        $object = new \stdClass();

        $metadata->getIdentifier()->willReturn(['field1', 'field2']);
        $metadata->getIdentifierValues($object)->willReturn(['field1' => 'value1', 'field2' => 'value2']);

        $this->getIdentity($object)->shouldReturn(['field1' => 'value1', 'field2' => 'value2']);
    }
}
