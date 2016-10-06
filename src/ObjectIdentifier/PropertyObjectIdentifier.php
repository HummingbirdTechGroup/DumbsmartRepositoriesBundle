<?php

namespace carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use Everzet\PersistedObjects\ObjectIdentifier;

class PropertyObjectIdentifier implements ObjectIdentifier
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $property;

    /**
     * @param string $className
     * @param string $property
     */
    public function __construct($className, $property)
    {
        $this->className = $className;
        $this->property = $property;
    }

    /**
     * @inheritDoc
     */
    public function getIdentity($object)
    {
        $property = new \ReflectionProperty($this->className, $this->property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
