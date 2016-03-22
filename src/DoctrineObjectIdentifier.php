<?php

namespace carlosV2\DumbsmartRepositoriesBundle;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Everzet\PersistedObjects\ObjectIdentifier;

class DoctrineObjectIdentifier implements ObjectIdentifier
{
    /**
     * @var ClassMetadata
     */
    private $metadata;

    /**
     * @param ClassMetadata $metadata
     */
    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity($object)
    {
        $id = $this->metadata->getIdentifierValues($object);
        if (count($id) === 1) {
            $id = reset($id);
        }

        return $id;
    }
}
