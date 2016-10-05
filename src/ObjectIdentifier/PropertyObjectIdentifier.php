<?php

namespace carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use Everzet\PersistedObjects\ObjectIdentifier;

class PropertyObjectIdentifier implements ObjectIdentifier
{
    /**
     * @var string
     */
    private $property;

    /**
     * @param string $property
     */
    public function __construct($property)
    {
        $this->property = $property;
    }

    /**
     * @inheritDoc
     */
    public function getIdentity($object)
    {
        $property = new \ReflectionProperty($object, $this->property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
