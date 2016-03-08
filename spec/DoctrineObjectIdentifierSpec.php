<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

class DoctrineObjectIdentifierSpec extends ObjectBehavior
{
    function it_computes_the_id_of_an_object(ClassMetadata $metadata)
    {
        $object = new \stdClass();

        $metadata->getIdentifierValues($object)->willReturn('id');

        $this->beConstructedWith($metadata);
        $this->getIdentity($object)->shouldReturn('id');
    }
}
