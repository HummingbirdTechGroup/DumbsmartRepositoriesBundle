<?php

namespace spec\carlosV2\DumbsmartRepositoriesBundle\Configurer;

use carlosV2\DumbsmartRepositoriesBundle\DoctrineObjectIdentifier;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;

class ObjectIdentifierFactorySpec extends ObjectBehavior
{
    function it_creates_a_DoctrineObjectIdentifier(ClassMetadata $metadata)
    {
        $this->createObjectIdentifier($metadata)->shouldBeAnInstanceOf(DoctrineObjectIdentifier::class);
    }
}
