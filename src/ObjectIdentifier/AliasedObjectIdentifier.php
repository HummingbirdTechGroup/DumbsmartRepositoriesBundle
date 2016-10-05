<?php

namespace carlosV2\DumbsmartRepositoriesBundle\ObjectIdentifier;

use Everzet\PersistedObjects\ObjectIdentifier;

class AliasedObjectIdentifier implements ObjectIdentifier
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var ObjectIdentifier
     */
    private $identifier;

    /**
     * @var string[]
     */
    private $aliases;

    /**
     * @param string           $className
     * @param ObjectIdentifier $identifier
     */
    public function __construct($className, ObjectIdentifier $identifier)
    {
        $this->className = $className;
        $this->identifier = $identifier;
        $this->aliases = [];
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->aliases[] = $alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity($object)
    {
        if (in_array(get_class($object), $this->aliases)) {
            return $this->getIdentityForAliasedObject($object);
        }

        return $this->identifier->getIdentity($object);
    }

    /**
     * @param object $object
     *
     * @return mixed
     */
    private function getIdentityForAliasedObject($object)
    {
        $originalObject = (new \ReflectionClass($this->className))->newInstanceWithoutConstructor();
        
        $this->copyFields($object, $originalObject);
        $id =$this->identifier->getIdentity($originalObject);
        $this->copyFields($originalObject, $object);

        return $id;
    }

    /**
     * @param object $from
     * @param object $to
     */
    private function copyFields($from, $to)
    {
        foreach ($this->getProperties($from) as $field => $value) {
            $this->setFieldValue($to, $field, $value);
        }
    }

    /**
     * @param object $object
     *
     * @return array
     */
    private function getProperties($object)
    {
        $properties = [];

        $reflection = new \ReflectionObject($object);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);

            $properties[$property->getName()] = $property->getValue($object);
        }

        return $properties;
    }

    /**
     * @param object $object
     * @param string $field
     * @param mixed  $value
     */
    private function setFieldValue($object, $field, $value)
    {
        try {
            $property = new \ReflectionProperty($object, $field);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        } catch (\ReflectionException $e) {
            // Ignore
        }
    }
}
